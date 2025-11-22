<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

require_once __DIR__ . '/../src/bootstrap.php';

require_once __DIR__ . '/../src/HttpClient.php';
require_once __DIR__ . '/../src/CloudflareClient.php';
require_once __DIR__ . '/../src/RenderService.php';
require_once __DIR__ . '/../src/ArticleScorer.php';
require_once __DIR__ . '/../src/Cache.php';
require_once __DIR__ . '/../src/RateLimiter.php';

require_once __DIR__ . '/../src/Advice.php';
require_once __DIR__ . '/../src/EmailRenderer.php';

require_once __DIR__ . '/../src/WordpressClient.php';
require_once __DIR__ . '/../src/NewsletterService.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path   = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

/** UI */
if ($method === 'GET' && ($path === '/' || $path === '')) {
    require __DIR__ . '/view-home.php';
    exit;
}

/** helpers */
function ts_or_null(?string $s): ?int {
    if (!$s) return null;
    $t = strtotime($s);
    return $t ? $t : null;
}

/** HEAD/RAW quick mtime */
function quick_remote_mtime(string $url): ?int
{
    $http = new HttpClient();

    // 1) HEAD Last-Modified
    $lm = $http->headLastModified($url);
    if ($lm) return $lm;

    // 2) RAW scurt
    try {
        $probe = $http->fetchRawLimited($url, 262144);
        $html  = $probe['html'] ?? '';

        if (preg_match('~<meta[^>]+property=["\']article:modified_time["\'][^>]+content=["\']([^"\']+)~i', $html, $m)) {
            if ($t = ts_or_null($m[1])) return $t;
        }
        if (preg_match('~"dateModified"\s*:\s*"([^"]+)"~i', $html, $m)) {
            if ($t = ts_or_null($m[1])) return $t;
        }
        if (preg_match('~<time[^>]+datetime=["\']([^"\']+)["\']~i', $html, $m)) {
            if ($t = ts_or_null($m[1])) return $t;
        }
    } catch (\Throwable $e) { /* ignore */ }

    return null;
}

/** /api/render (POST) */
if ($path === '/api/render' && $method === 'POST') {
    $raw = file_get_contents('php://input') ?: '';
    $in  = json_decode($raw, true) ?? [];
    $url = trim((string)($in['url'] ?? ''));

    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $url)) {
        json_out(['error' => 'URL invalid'], 400);
    }

    $http = new HttpClient();

    try {
        // 1) Fetch simplu
        $probe = $http->fetchRaw($url);
        $det   = HttpClient::detectJsHeavy($probe['html']);

        // dacă nu e JS-heavy și avem conținut text OK, întoarcem varianta RAW
        if ($probe['status'] === 200 && !$det['heavy'] && $det['metrics']['textLen'] >= 400) {
            json_out([
                'html_source' => 'raw',
                'bytes'       => $probe['bytes'],
                'suspect_js'  => $det,
                'snippet'     => mb_substr(strip_tags($probe['html']), 0, 180) . '…'
            ]);
        }

        // 2) Fallback → Cloudflare Browser Rendering
        $cfId = envget('CF_ACCOUNT_ID');
        $cfTk = envget('CF_API_TOKEN');
        if (!$cfId || !$cfTk) {
            // dacă nu avem token, tot întoarcem RAW cu mențiune
            json_out([
                'html_source' => 'raw',
                'bytes'       => $probe['bytes'],
                'suspect_js'  => $det,
                'note'        => 'Cloudflare indisponibil (token lipsă); rezultat RAW',
                'snippet'     => mb_substr(strip_tags($probe['html']), 0, 180) . '…'
            ]);
        }

        $client   = new CloudflareClient($cfId, $cfTk);
        $rendered = $client->fetchRenderedHtml($url);

        json_out([
            'html_source' => 'rendered',
            'bytes'       => strlen($rendered),
            'suspect_js'  => $det,
            'snippet'     => mb_substr(strip_tags($rendered), 0, 180) . '…'
        ]);

    } catch (\Throwable $e) {
        // dacă a picat fetch-ul sau Cloudflare, răspunde onest
        json_out(['error' => $e->getMessage()], 502);
    }
}

/** /api/score (POST) */
if ($path === '/api/score' && $method === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $raw = file_get_contents('php://input') ?: '';
    $in  = json_decode($raw, true) ?? [];
    $url = trim((string)($in['url'] ?? ''));

    // context: 'article' (default) sau 'local'
    $context = (string)($in['context'] ?? 'article');
    if ($context !== 'local') {
        $context = 'article';
    }

    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $url)) {
        http_response_code(400);
        echo json_encode(['error' => 'URL invalid'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // rate-limit
    $rl = new RateLimiter(__DIR__ . '/../storage/ratelimit', (int) envget('RATE_LIMIT_SCORE', '200'));
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $probe = $rl->hit("score:$ip");
    if (!$probe['allowed']) {
        http_response_code(429);
        echo json_encode(['error' => 'Prea multe cereri astăzi.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // cache
    $ttl   = max(0, (int) envget('CACHE_TTL', '900'));
    $fresh = !empty($in['fresh']);
    $cache = $ttl > 0 ? new Cache(__DIR__ . '/../storage/cache', $ttl) : null;
    // includem contextul în cheie ca să nu amestecăm articol vs local
    $key   = 'score:v6:' . $context . ':deep:' . $url;

    if (!$fresh && $ttl > 0 && ($cached = $cache->get($key))) {
        $cachedAt    = (int) ($cached['cached_at'] ?? 0);
        $cachedMtime = (int) ($cached['content_mtime'] ?? 0);
        $remoteMtime = quick_remote_mtime($url);
        $changed     = ($remoteMtime && $cachedMtime && $remoteMtime > $cachedMtime);

        if (!$changed && (time() - $cachedAt) < $ttl) {
            echo json_encode($cached, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // deep render
    $cfId = envget('CF_ACCOUNT_ID');
    $cfTk = envget('CF_API_TOKEN');
    $cf   = ($cfId && $cfTk) ? new CloudflareClient($cfId, $cfTk) : null;

    $renderer = new RenderService(new HttpClient(), $cf);
    $render   = $renderer->getHtml($url, ['force_render' => true]);

    $score = ArticleScorer::score($render['html'], $url, 'deep', [
        'context' => $context,
    ]);

    $meta  = $score['meta'] ?? [];
    $mtime = null;
    if (!empty($meta['dateModified']))  $mtime = ts_or_null($meta['dateModified']);
    if (!$mtime && !empty($meta['datePublished'])) $mtime = ts_or_null($meta['datePublished']);

    $response = [
        'mode'          => 'deep',
        'source'        => $render['source'],
        'js'            => $render['js'],
        'score'         => $score,
        'context'       => $context,
        'cached_at'     => time(),
        'content_mtime' => $mtime ?: 0,
    ];

    if ($ttl > 0) {
        $cache?->set($key, $response);
    }

    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/** /api/email-report (POST) */
if ($path === '/api/email-report' && $method === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $raw  = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

        $url       = trim((string)($data['url'] ?? ''));
        $email     = trim((string)($data['email'] ?? ''));
        $firstName = trim((string)($data['first_name'] ?? ''));
        $consentNewsletter = (bool)($data['consent_newsletter'] ?? false);
        $consentTerms      = (bool)($data['consent_terms'] ?? false);

        if ($url === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Lipsește URL sau email.']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Email invalid.']);
            exit;
        }

        // 1) Render + score proaspăt
        $cfId = envget('CF_ACCOUNT_ID');
        $cfTk = envget('CF_API_TOKEN');
        $cf   = ($cfId && $cfTk) ? new CloudflareClient($cfId, $cfTk) : null;

        $renderer = new RenderService(new HttpClient(), $cf);
        $render   = $renderer->getHtml($url, ['force_render' => true]);
        $score    = ArticleScorer::score($render['html'], $url, 'deep');

        $title = (string)($score['meta']['title'] ?? '');
        $total = (int)($score['total'] ?? 0);

        // 2) Email body (HTML + TXT)
        $emailData = [
            'source' => $render['source'] ?? '',
            'score'  => $score,
        ];
        $htmlBody = EmailRenderer::renderHtml($url, $emailData);
        $txtBody  = EmailRenderer::renderText($url, $emailData);

        // 3) Trimitem emailul
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = (string) envget('SMTP_HOST');
            $mail->Port       = (int) envget('SMTP_PORT', '587');
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = (string) envget('SMTP_SECURE', 'tls'); // tls/ssl
            $mail->Username   = (string) envget('SMTP_USER');
            $mail->Password   = (string) envget('SMTP_PASS');
            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = 'base64';

            $fromEmail = (string) envget('MAIL_FROM', 'reports@novaweb.ro');
            $fromName  = (string) envget('MAIL_FROM_NAME', 'Novaweb SEO Checker');

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($email, $firstName ?: '');
            $mail->Subject = sprintf('Raport SEO — %d/100', $total);
            $mail->isHTML(true);
            $mail->Body    = $htmlBody;
            $mail->AltBody = $txtBody;
            $mail->send();
        } catch (MailerException $e) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Email fail: '.$e->getMessage()]);
            exit;
        }

        // 4) Înrolare în MailPoet (dacă avem consimțuri)
        $wpUrl   = (string) envget('WP_SUBSCRIBE_URL', '');
        $wpTok   = (string) envget('WP_SUBSCRIBE_TOKEN', '');
        $wpList  = (string) envget('WP_LIST_NAME', 'SEO Checker Leads');
        $wpInsec = (bool) ((int) envget('WP_INSECURE', '0') === 1);

        $newsletterInfo = ['skipped' => true, 'ok' => false, 'reason' => 'missing_config'];

        if ($wpUrl && $wpTok) {
            $wpClient   = new WordpressClient($wpUrl, $wpTok, $wpInsec);
            $newsletter = new NewsletterService($wpClient, $wpList);
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

            $newsletterInfo = $newsletter->subscribeIfConsented(
                email: $email,
                firstName: $firstName !== '' ? $firstName : null,
                consentNewsletter: $consentNewsletter,
                consentTerms:      $consentTerms,
                ip: $ip
            );
        }

        echo json_encode(['ok' => true, 'newsletter' => $newsletterInfo], JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

/** 404 */
http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'Not Found', 'path' => $path], JSON_UNESCAPED_UNICODE);
exit;
