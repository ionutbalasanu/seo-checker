<?php
declare(strict_types=1);

final class RenderService
{
    /** @var HttpClient */
    private $http;
    /** @var CloudflareClient|null */
    private $cf;

    public function __construct($http, ?CloudflareClient $cf = null)
    {
        // nu impun tipul la runtime ca să nu crape dacă schimbi implementarea
        $this->http = $http;
        $this->cf   = $cf;
    }

    /**
     * Render profunzime (CF) cu fallback HTTP simplu.
     * @return array{html:string, source:string, js:bool}
     */
    public function getHtml(string $url, array $options = []): array
    {
        $forceRender = !empty($options['force_render']); // dacă e true, încercăm CF prima dată
        $waitFor     = $options['waitFor'] ?? null;       // ignorat de fallback HTTP

        // 1) încearcă Cloudflare dacă e disponibil
        if ($this->cf) {
            try {
                // dacă ai semnături custom pe CloudflareClient, adaptează parametrii aici
                $html = $this->cf->fetchRenderedHtml($url, $waitFor);
                if (!is_string($html) || $html === '') {
                    throw new \RuntimeException('Cloudflare returned empty HTML');
                }
                return [
                    'html'   => $html,
                    'source' => 'cloudflare',
                    'js'     => true,
                ];
            } catch (\Throwable $e) {
                // dacă e „force_render” am încercat oricum; cădem pe HTTP simplu
                // (nu propagăm excepția aici ca să avem fallback robust)
            }
        }

        // 2) fallback HTTP simplu (fără JS)
        $html = $this->httpGet($url);
        return [
            'html'   => $html,
            'source' => 'http',
            'js'     => false,
        ];
    }

    /**
     * GET robust fără să depindem de o metodă specifică în HttpClient.
     * Încearcă întâi metode existente pe $this->http, apoi cURL, apoi file_get_contents.
     */
    private function httpGet(string $url): string
    {
        // a) dacă HttpClient are fetchRaw() -> folosește-l
        if (method_exists($this->http, 'fetchRaw')) {
            $resp = $this->http->fetchRaw($url);
            if (is_array($resp) && isset($resp['html'])) return (string)$resp['html'];
            if (is_string($resp)) return $resp;
        }

        // b) dacă (poate) are get() -> try/catch
        if (method_exists($this->http, 'get')) {
            try {
                $resp = $this->http->get($url);
                if (is_array($resp) && isset($resp['html'])) return (string)$resp['html'];
                if (is_string($resp)) return $resp;
            } catch (\Throwable $e) {
                // continuăm cu cURL
            }
        }

        // c) cURL simplu
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_USERAGENT      => 'Novaweb-SEO-Checker/1.0 (+https://novaweb.ro)',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_HTTPHEADER     => ['Accept: text/html,*/*;q=0.8'],
            ]);
            $body = curl_exec($ch);
            $err  = curl_error($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ($body === false) {
                throw new \RuntimeException('HTTP GET (cURL) failed: ' . $err);
            }
            if ($code >= 400) {
                throw new \RuntimeException('HTTP error ' . $code);
            }
            return (string) $body;
        }

        // d) fallback minimal
        $ctx = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => 20,
                'header'  => "User-Agent: Novaweb-SEO-Checker/1.0\r\nAccept: text/html,*/*;q=0.8\r\n",
            ]
        ]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body === false) {
            throw new \RuntimeException('HTTP GET failed (file_get_contents)');
        }
        return (string) $body;
    }
}
