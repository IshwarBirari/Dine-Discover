<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/yelp_client.php';
require_once __DIR__ . '/repositories/favorites_repo.php';
require_once __DIR__ . '/repositories/logs_repo.php';

session_start();
if (empty($_SESSION['session_key'])) {
  $_SESSION['session_key'] = bin2hex(random_bytes(16));
}
if (!is_dir(APP_CACHE_DIR)) {
  @mkdir(APP_CACHE_DIR, 0775, true);
}
