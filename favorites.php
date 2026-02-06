<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$title = 'Favorites • ' . APP_NAME;

$items = [];
$error = null;

try {
  $items = favorites_all($_SESSION['session_key']);
} catch (Throwable $t) {
  $error = $t->getMessage();
}

$flash = flash_get();
include __DIR__ . '/partials/head.php';
?>

<div class="card">
  <h2 style="margin:0 0 10px 0;">Your favorites</h2>

  <?php if ($flash): ?>
    <div class="notice <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="notice error"><?= e($error) ?></div>
  <?php elseif (!$items): ?>
    <div class="notice">No favorites yet. Search and add some.</div>
  <?php else: ?>
    <div class="results">
      <?php foreach ($items as $f):
        $id = (string)$f['business_id'];
        $name = (string)$f['business_name'];
        $url = (string)($f['business_url'] ?? '');
      ?>
        <div class="item">
          <img src="https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=800&q=60" alt="placeholder"/>
          <div class="content">
            <div class="title"><?= e($name) ?></div>
            <div class="meta">Saved: <?= e((string)$f['created_at']) ?></div>
            <div class="actions">
              <a class="btn primary" href="details.php?id=<?= urlencode($id) ?>">Details</a>
              <?php if ($url): ?><a class="btn" target="_blank" rel="noreferrer" href="<?= e($url) ?>">Yelp</a><?php endif; ?>
              <form method="post" action="remove_favorite.php">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"/>
                <input type="hidden" name="business_id" value="<?= e($id) ?>"/>
                <input type="hidden" name="redirect" value="favorites.php"/>
                <button class="btn danger" type="submit">Remove</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php include __DIR__ . '/partials/foot.php'; ?>
