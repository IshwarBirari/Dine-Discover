<?php
declare(strict_types=1);

function e(string $v): string {
  return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function get_str(string $key, int $maxLen = 255, string $default = ''): string {
  $val = $_GET[$key] ?? $default;
  if (!is_string($val)) return $default;
  $val = trim($val);
  if ($val === '') return $default;
  return mb_substr($val, 0, $maxLen);
}

function get_int(string $key, int $min, int $max, int $default): int {
  $val = $_GET[$key] ?? $default;
  if (is_array($val) || !is_numeric($val)) return $default;
  $n = (int)$val;
  if ($n < $min || $n > $max) return $default;
  return $n;
}

function get_bool(string $key): bool {
  $val = $_GET[$key] ?? null;
  return $val === '1' || $val === 'true' || $val === 'on';
}

function get_price_filter(): ?string {
  $price = $_GET['price'] ?? '';
  if (!is_string($price)) return null;
  $price = trim($price);
  if ($price === '') return null;
  if (!preg_match('/^[1-4](,[1-4])*$/', $price)) return null;
  return $price;
}

function flash_set(string $type, string $message): void {
  $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array {
  if (!isset($_SESSION['flash'])) return null;
  $f = $_SESSION['flash'];
  unset($_SESSION['flash']);
  return is_array($f) ? $f : null;
}
