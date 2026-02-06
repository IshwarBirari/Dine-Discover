<?php
declare(strict_types=1);

define('APP_NAME', env('APP_NAME', 'DineDiscover') ?? 'DineDiscover');
define('APP_ENV', env('APP_ENV', 'local') ?? 'local');

define('YELP_API_KEY', env('YELP_API_KEY', '') ?? '');
define('YELP_API_BASE', 'https://api.yelp.com/v3');

define('APP_CACHE_DIR', dirname(__DIR__) . '/public/cache');
define('CACHE_TTL_SECONDS', 600);

define('DEFAULT_LIMIT', 12);
