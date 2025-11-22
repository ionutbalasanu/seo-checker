<?php
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

    // 2) Fallback → Cloudflare Browser Rendering (/content)
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

    $client = new CloudflareClient($cfId, $cfTk);
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
