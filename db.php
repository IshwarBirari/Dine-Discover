<?php
declare(strict_types=1);

function db(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $host = env('DB_HOST', '127.0.0.1') ?? '127.0.0.1';
  $name = env('DB_NAME', 'dine_discover') ?? 'dine_discover';
  $user = env('DB_USER', 'root') ?? 'root';
  $pass = env('DB_PASS', '') ?? '';

  $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  return $pdo;
}
