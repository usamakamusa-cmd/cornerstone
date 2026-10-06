<?php
require_once __DIR__ . '/config.php';

$pageTitle = APP_NAME . ' | Menu';
$categories = fetch_categories($pdo);
$menuItems = fetch_menu_items($pdo, true);

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container section-heading">
        <div>
            <span class="section-tag"><i class="fa-solid fa-book-open"></i> Dynamic Menu</span>
            <h1>Browse our full restaurant menu</h1>
            <p class="muted">Search meals, filter by category, and order instantly through WhatsApp.</p>
        </div>
        <a class="btn btn-primary" href="<?= e(site_url('order.php')) ?>"><i class="fa-solid fa-cart-shopping"></i> Go to Order Form</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="menu-toolbar">
            <div class="form-group" style="flex: 1; margin: 0;">
                <label for="menuSearch">Search menu items</label>
                <input class="input" type="search" id="menuSearch" placeholder="Search food, category, or description...">
            </div>
        </div>

        <div class="filter-bar">
            <button class="filter-chip active" data-filter="all">All</button>
            <?php foreach ($categories as $category): ?>
                <button class="filter-chip" data-filter="<?= e(strtolower($category['name'])) ?>"><?= e($category['name']) ?></button>
            <?php endforeach; ?>
        </div>

        <div class="menu-grid">
            <?php if ($menuItems): ?>
                <?php foreach ($menuItems as $item): ?>
                    <article class="menu-card menu-filter-card" data-category="<?= e(strtolower($item['category_name'] ?? 'uncategorized')) ?>" data-search="<?= e(strtolower($item['name'] . ' ' . $item['description'] . ' ' . ($item['category_name'] ?? ''))) ?>">
                        <img src="<?= e(menu_image($item['image'])) ?>" alt="<?= e($item['name']) ?>" loading="lazy">
                        <div class="menu-card-content">
                            <div class="card-top">
                                <div>
                                    <h3><?= e($item['name']) ?></h3>
                                    <span class="pill"><?= e($item['category_name'] ?? 'Meal') ?></span>
                                </div>
                                <span class="menu-price"><?= e(currency_ugx((float) $item['price'])) ?></span>
                            </div>
                            <p class="muted"><?= e($item['description']) ?></p>
                            <div class="menu-meta">
                                <span class="status-pill <?= $item['is_available'] ? 'status-available' : 'status-unavailable' ?>">
                                    <?= $item['is_available'] ? 'Available now' : 'Currently unavailable' ?>
                                </span>
                            </div>
                            <div class="hero-actions">
                                <a class="btn btn-primary btn-sm" href="<?= e(site_url('order.php?food=' . (int) $item['id'])) ?>">Order Now</a>
                                <a class="btn btn-success btn-sm" target="_blank" rel="noopener" href="<?= e(whatsapp_item_link($item['name'])) ?>">WhatsApp Order</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card empty-state" style="grid-column: 1/-1;">No menu items found. Add some from the admin dashboard.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
