<?php
declare(strict_types=1);

function yelp_request(string $path, array $query = []): array {
  if (YELP_API_KEY === '') {
    throw new RuntimeException('Missing Yelp API key. Set YELP_API_KEY in .env');
  }

  $url = YELP_API_BASE . $path;
  if (!empty($query)) $url .= '?' . http_build_query($query);

  $cacheKey = hash('sha256', $url);
  $cacheFile = APP_CACHE_DIR . '/' . $cacheKey . '.json';

  if (is_readable($cacheFile) && (time() - filemtime($cacheFile) < CACHE_TTL_SECONDS)) {
    $raw = file_get_contents($cacheFile);
    $data = json_decode($raw, true);
    if (is_array($data)) return $data;
  }

  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . YELP_API_KEY,
      'Accept: application/json',
    ],
    CURLOPT_TIMEOUT => 15,
  ]);

  $resp = curl_exec($ch);
  $err = curl_error($ch);
  $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($resp === false) throw new RuntimeException('Yelp request failed: ' . $err);

  $data = json_decode($resp, true);
  if (!is_array($data)) throw new RuntimeException('Invalid JSON from Yelp');

  if ($code >= 400) {
    $msg = $data['error']['description'] ?? ($data['error']['code'] ?? 'Unknown Yelp error');
    throw new RuntimeException("Yelp API error ({$code}): " . $msg);
  }

  @file_put_contents($cacheFile, json_encode($data));
  return $data;
}

function yelp_search(string $term, string $location, array $filters = []): array {
  $query = [
    'term' => $term,
    'location' => $location,
    'limit' => $filters['limit'] ?? DEFAULT_LIMIT,
    'offset' => $filters['offset'] ?? 0,
  ];
  if (!empty($filters['price'])) $query['price'] = $filters['price'];
  if (!empty($filters['open_now'])) $query['open_now'] = true;

  return yelp_request('/businesses/search', $query);
}

function yelp_business(string $businessId): array {
  return yelp_request('/businesses/' . rawurlencode($businessId));
}
