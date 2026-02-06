<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$title = 'Search • ' . APP_NAME;

$term = get_str('term', 80, '');
$location = get_str('location', 120, '');
$price = get_price_filter();
$openNow = get_bool('open_now');
$minRating = get_int('min_rating', 1, 5, 1);
$offset = get_int('offset', 0, 1000, 0);

$results = null;
$error = null;

if ($term !== '' && $location !== '') {
  try {
    $filters = [
      'price' => $price,
      'open_now' => $openNow,
      'min_rating' => $minRating,
      'limit' => DEFAULT_LIMIT,
      'offset' => $offset,
    ];
    logs_add($_SESSION['session_key'], $term, $location, $filters);

    $api = yelp_search($term, $location, $filters);
    $items = $api['businesses'] ?? [];

    // apply min rating filter client-side
    $filtered = [];
    foreach ($items as $b) {
      $rating = (float)($b['rating'] ?? 0);
      if ($rating >= $minRating) $filtered[] = $b;
    }
    $results = $filtered;
  } catch (Throwable $t) {
    $error = $t->getMessage();
  }
}

$flash = flash_get();
include __DIR__ . '/partials/head.php';
?>

<div class="card">
  <div class="grid">
    <div>
      <h2 style="margin:0 0 10px 0;">Find restaurants</h2>
      <form method="get" data-search-form>
        <div class="row">
          <div>
            <label>Search term</label>
            <input name="term" value="<?= e($term) ?>" placeholder="e.g., biryani, coffee, pizza" required/>
          </div>
          <div>
            <label>Location</label>
            <input name="location" value="<?= e($location) ?>" placeholder="e.g., Arlington, TX or 76010" required/>
          </div>
        </div>

        <div class="row" style="margin-top:12px;">
          <div>
            <label>Price (Yelp)</label>
            <select name="price">
              <option value="" <?= $price===null?'selected':'' ?>>Any</option>
              <option value="1" <?= $price==='1'?'selected':'' ?>>$</option>
              <option value="2" <?= $price==='2'?'selected':'' ?>>$$</option>
              <option value="3" <?= $price==='3'?'selected':'' ?>>$$$</option>
              <option value="4" <?= $price==='4'?'selected':'' ?>>$$$$</option>
              <option value="1,2" <?= $price==='1,2'?'selected':'' ?>>$ - $$</option>
              <option value="2,3" <?= $price==='2,3'?'selected':'' ?>>$$ - $$$</option>
              <option value="3,4" <?= $price==='3,4'?'selected':'' ?>>$$$ - $$$$</option>
            </select>
          </div>
          <div>
            <label>Minimum rating</label>
            <select name="min_rating">
              <?php for ($i=1;$i<=5;$i++): ?>
                <option value="<?= $i ?>" <?= $minRating===$i?'selected':'' ?>><?= $i ?>+</option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="actions">
          <label style="display:flex;align-items:center;gap:10px;margin:0;">
            <input type="checkbox" name="open_now" value="1" <?= $openNow?'checked':'' ?> style="width:auto;"/>
            Open now
          </label>

          <button class="btn primary" type="submit">Search</button>
          <a class="btn" href="index.php">Reset</a>
          <div class="small" data-loading style="display:none;">Searching…</div>
        </div>
      </form>

      <?php if ($flash): ?>
        <div class="notice <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="notice error"><?= e($error) ?></div>
      <?php elseif ($term !== '' && $location !== '' && is_array($results) && count($results)===0): ?>
        <div class="notice">No results. Try different keywords or location.</div>
      <?php else: ?>
        <div class="notice">Tip: Try “biryani”, “tacos”, “coffee”, “indian”.</div>
      <?php endif; ?>
    </div>

    <div>
      <h2 style="margin:0 0 10px 0;">Filters preview</h2>
      <div class="pills">
        <div class="pill">Limit: <?= DEFAULT_LIMIT ?></div>
        <div class="pill">Price: <?= e($price ?? 'any') ?></div>
        <div class="pill">Open: <?= $openNow ? 'true' : 'false' ?></div>
        <div class="pill">Min rating: <?= (int)$minRating ?>+</div>
      </div>
      <div class="hr"></div>
      <div class="small">
        Searches are logged in MySQL for analytics. Favorites are stored per browser session.
      </div>
    </div>
  </div>
</div>

<?php if (is_array($results)): ?>
  <div class="results">
    <?php foreach ($results as $b):
      $id = (string)($b['id'] ?? '');
      $name = (string)($b['name'] ?? 'Unknown');
      $img = (string)($b['image_url'] ?? '');
      $rating = (string)($b['rating'] ?? '');
      $priceTag = (string)($b['price'] ?? '');
      $addr = '';
      if (!empty($b['location']['display_address']) && is_array($b['location']['display_address'])) {
        $addr = implode(', ', $b['location']['display_address']);
      }
      $categories = '';
      if (!empty($b['categories']) && is_array($b['categories'])) {
        $cats = array_map(fn($c) => $c['title'] ?? '', $b['categories']);
        $categories = implode(' • ', array_filter($cats));
      }
    ?>
      <div class="item">
        <?php if ($img): ?>
          <img src="<?= e($img) ?>" alt="<?= e($name) ?>"/>
        <?php else: ?>
          <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=800&q=60" alt="placeholder"/>
        <?php endif; ?>
        <div class="content">
          <div class="title"><?= e($name) ?></div>
          <div class="meta"><?= e($categories) ?></div>
          <div class="meta">⭐ <?= e($rating) ?> <?= $priceTag ? ' • ' . e($priceTag) : '' ?></div>
          <div class="meta"><?= e($addr) ?></div>
          <div class="actions">
            <a class="btn primary" href="details.php?id=<?= urlencode($id) ?>">Details</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php
    $prevOffset = max(0, $offset - DEFAULT_LIMIT);
    $nextOffset = $offset + DEFAULT_LIMIT;
    $baseParams = $_GET;
  ?>
  <div class="actions" style="margin-top:14px;">
    <?php if ($offset > 0): $baseParams['offset'] = $prevOffset; ?>
      <a class="btn" href="index.php?<?= e(http_build_query($baseParams)) ?>">← Prev</a>
    <?php endif; ?>
    <?php if (count($results) === DEFAULT_LIMIT): $baseParams['offset'] = $nextOffset; ?>
      <a class="btn" href="index.php?<?= e(http_build_query($baseParams)) ?>">Next →</a>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/partials/foot.php'; ?>
