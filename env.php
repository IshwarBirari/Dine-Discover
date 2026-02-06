<?php
declare(strict_types=1);

function env(string $key, ?string $default = null): ?string {
  static $loaded = false;
  static $vars = [];

  if (!$loaded) {
    $loaded = true;
    $envPath = dirname(__DIR__) . '/.env';
    if (is_readable($envPath)) {
      $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $k = trim(substr($line, 0, $pos));
        $v = trim(substr($line, $pos + 1));
        if ((str_starts_with($v, '"') && str_ends_with($v, '"')) ||
            (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
          $v = substr($v, 1, -1);
        }
        $vars[$k] = $v;
      }
    }
  }
  return $vars[$key] ?? $default;
}
