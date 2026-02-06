<?php
declare(strict_types=1);

function csrf_token(): string {
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['_csrf'];
}

function csrf_validate(?string $token): bool {
  if (empty($_SESSION['_csrf'])) return false;
  if (!is_string($token) || $token === '') return false;
  return hash_equals($_SESSION['_csrf'], $token);
}
