<?php
declare(strict_types=1);

final class HttpClient
{
    public function fetchRaw(string $url): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => 'Novaweb-SEO-Checker/0.1',
            CURLOPT_HEADER => true,
        ]);
        $out  = curl_exec($ch);
        $err  = curl_errno($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $headerSize = (int)($info['header_size'] ?? 0);
        $headersRaw = substr((string)$out, 0, $headerSize);
        $body       = substr((string)$out, $headerSize);

        return [
            'status' => (int)($info['http_code'] ?? 0),
            'bytes'  => strlen((string)$out),
            'headers'=> $headersRaw,
            'html'   => $body
        ];
    }

    /**
     * Ca fetchRaw, dar limitează transferul (range) la max $maxBytes (default ~256KB).
     */
    public function fetchRawLimited(string $url, int $maxBytes = 262144): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_USERAGENT => 'Novaweb-SEO-Checker/0.1',
            CURLOPT_HEADER => true,
            CURLOPT_RANGE => '0-'.max(1,$maxBytes-1)
        ]);
        $out  = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $headerSize = (int)($info['header_size'] ?? 0);
        $headersRaw = substr((string)$out, 0, $headerSize);
        $body       = substr((string)$out, $headerSize);

        return [
            'status' => (int)($info['http_code'] ?? 0),
            'bytes'  => strlen((string)$out),
            'headers'=> $headersRaw,
            'html'   => $body
        ];
    }

    /**
     * HEAD – încearcă să întoarcă Last-Modified ca timestamp.
     */
    public function headLastModified(string $url): ?int {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT => 'Novaweb-SEO-Checker/0.1',
            CURLOPT_HEADER => true,
        ]);
        $out  = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if (!$out || (int)($info['http_code'] ?? 0) < 200) return null;

        if (preg_match('~^Last-Modified:\s*(.+)$~im', (string)$out, $m)) {
            $t = strtotime(trim($m[1]));
            return $t ?: null;
        }
        return null;
    }

    /** Heuristic JS-heavy */
    public static function detectJsHeavy(string $html): array {
        $scripts  = substr_count($html, '<script');
        $textLen  = strlen(strip_tags($html));
        $totalLen = strlen($html);

        $heavy = ($scripts > 40 && $textLen < 10000);
        $textRatio = $totalLen > 0 ? $textLen / $totalLen : 0.0;

        return [
            'heavy'   => $heavy,
            'metrics' => [
                'scriptCount' => $scripts,
                'textLen'     => $textLen,
                'textRatio'   => $textRatio,
            ],
        ];
    }
}
