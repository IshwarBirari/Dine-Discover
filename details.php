<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$title = 'Details • ' . APP_NAME;

$id = get_str('id', 80, '');
if ($id === '') {
  flash_set('error', 'Missing restaurant id.');
  header('Location: index.php');
  exit;
}

$error = null;
$data = null;
$isFav = false;

try {
  $data = yelp_business($id);
  $isFav = favorites_exists($_SESSION['session_key'], $id);
} catch (Throwable $t) {
  $error = $t->getMessage();
}

include __DIR__ . '/partials/head.php';
?>

<div class="card">
  <?php if ($error): ?>
    <div class="notice error"><?= e($error) ?></div>
    <div class="actions">
      <a class="btn" href="index.php">Back</a>
    </div>
  <?php else: ?>
    <?php
      $name = (string)($data['name'] ?? 'Unknown');
      $img = (string)($data['image_url'] ?? '');
      $rating = (string)($data['rating'] ?? '');
      $phone = (string)($data['display_phone'] ?? '');
      $url = (string)($data['url'] ?? '');
      $price = (string)($data['price'] ?? '');
      $addr = '';
      if (!empty($data['location']['display_address']) && is_array($data['location']['display_address'])) {
        $addr = implode(', ', $data['location']['display_address']);
      }
      $cats = '';
      if (!empty($data['categories']) && is_array($data['categories'])) {
        $cc = array_map(fn($c)=>$c['title'] ?? '', $data['categories']);
        $cats = implode(' • ', array_filter($cc));
      }
      $isOpen = $data['hours'][0]['is_open_now'] ?? null;
    ?>

    <div class="grid">
      <div>
        <h2 style="margin:0 0 6px 0;"><?= e($name) ?></h2>
        <div class="pills">
          <?php if ($cats): ?><div class="pill"><?= e($cats) ?></div><?php endif; ?>
          <?php if ($price): ?><div class="pill">Price: <?= e($price) ?></div><?php endif; ?>
          <?php if ($rating): ?><div class="pill">⭐ <?= e($rating) ?></div><?php endif; ?>
          <?php if (is_bool($isOpen)): ?><div class="pill"><?= $isOpen ? 'Open now' : 'Closed now' ?></div><?php endif; ?>
        </div>

        <div class="hr"></div>
        <div class="meta">
          <div><strong>Address:</strong> <?= e($addr) ?></div>
          <?php if ($phone): ?><div><strong>Phone:</strong> <?= e($phone) ?></div><?php endif; ?>
          <?php if ($url): ?><div><strong>Yelp:</strong> <a class="btn" href="<?= e($url) ?>" target="_blank" rel="noreferrer">Open on Yelp</a></div><?php endif; ?>
        </div>

        <div class="hr"></div>

        <div class="actions">
          <form method="post" action="<?= $isFav ? 'remove_favorite.php' : 'add_favorite.php' ?>">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"/>
            <input type="hidden" name="business_id" value="<?= e($id) ?>"/>
            <input type="hidden" name="business_name" value="<?= e($name) ?>"/>
            <input type="hidden" name="business_url" value="<?= e($url) ?>"/>
            <button class="btn <?= $isFav ? 'danger' : 'primary' ?>" type="submit">
              <?= $isFav ? 'Remove from favorites' : 'Add to favorites' ?>
            </button>
          </form>
          <a class="btn" href="index.php">Back</a>
        </div>
      </div>

      <div>
        <div class="item">
          <?php if ($img): ?>
            <img src="<?= e($img) ?>" alt="<?= e($name) ?>"/>
          <?php else: ?>
            <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=60" alt="placeholder"/>
          <?php endif; ?>
          <div class="content">
            <div class="meta">Coordinates: <?= e((string)($data['coordinates']['latitude'] ?? '')) ?>, <?= e((string)($data['coordinates']['longitude'] ?? '')) ?></div>
            <div class="meta">Reviews count: <?= e((string)($data['review_count'] ?? '')) ?></div>
            <?php if (!empty($data['transactions']) && is_array($data['transactions'])): ?>
              <div class="meta">Transactions: <?= e(implode(', ', $data['transactions'])) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

  <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/foot.php'; ?>
