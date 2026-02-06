<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: favorites.php'); exit;
}

$token = $_POST['_csrf'] ?? null;
if (!csrf_validate(is_string($token) ? $token : null)) {
  flash_set('error', 'Invalid CSRF token.');
  header('Location: favorites.php'); exit;
}

$businessId = is_string($_POST['business_id'] ?? null) ? trim($_POST['business_id']) : '';
if ($businessId === '') {
  flash_set('error', 'Missing business id.');
  header('Location: favorites.php'); exit;
}

try {
  favorites_remove($_SESSION['session_key'], $businessId);
  flash_set('success', 'Removed from favorites.');
} catch (Throwable $t) {
  flash_set('error', 'Could not remove: ' . $t->getMessage());
}

$redirect = is_string($_POST['redirect'] ?? null) ? $_POST['redirect'] : 'favorites.php';
header('Location: ' . $redirect);
