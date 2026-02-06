<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php'); exit;
}

$token = $_POST['_csrf'] ?? null;
if (!csrf_validate(is_string($token) ? $token : null)) {
  flash_set('error', 'Invalid CSRF token.');
  header('Location: index.php'); exit;
}

$businessId = is_string($_POST['business_id'] ?? null) ? trim($_POST['business_id']) : '';
$name = is_string($_POST['business_name'] ?? null) ? trim($_POST['business_name']) : 'Unknown';
$url = is_string($_POST['business_url'] ?? null) ? trim($_POST['business_url']) : null;

if ($businessId === '') {
  flash_set('error', 'Missing business id.');
  header('Location: index.php'); exit;
}

try {
  favorites_add($_SESSION['session_key'], $businessId, $name, $url);
  flash_set('success', 'Added to favorites.');
} catch (Throwable $t) {
  flash_set('error', 'Could not add favorite: ' . $t->getMessage());
}

header('Location: details.php?id=' . urlencode($businessId));
