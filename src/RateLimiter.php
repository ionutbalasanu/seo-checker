<?php
declare(strict_types=1);

final class RateLimiter {
  private string $dir;
  private int $limit;
  public function __construct(string $dir, int $limitPerDay = 200) {
    $this->dir = rtrim($dir, '/\\');
    $this->limit = $limitPerDay;
    if (!is_dir($this->dir)) @mkdir($this->dir, 0775, true);
  }
  private function keyPath(string $key): string {
    $day = gmdate('Ymd'); // reset la 00:00 UTC
    return "{$this->dir}/{$day}_" . sha1($key) . ".cnt";
  }
  public function hit(string $key): array {
    $f = $this->keyPath($key);
    $n = 0;
    if (is_file($f)) { $n = (int)file_get_contents($f); }
    $n++;
    @file_put_contents($f, (string)$n, LOCK_EX);
    return ['count'=>$n, 'limit'=>$this->limit, 'allowed' => $n <= $this->limit];
  }
}