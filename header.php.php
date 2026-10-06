<?php
if (!isset($pageTitle)) {
    $pageTitle = APP_NAME;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="Premium digital menu board and restaurant ordering website for <?= e(APP_NAME) ?> in Uganda.">
    <meta name="keywords" content="restaurant menu Uganda, digital menu board, WhatsApp food ordering, Cornerstone Restaurant Mafubira">
    <meta name="author" content="Kamusa Usama">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="Fresh meals, fast service, great taste. View the menu and order through WhatsApp.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(full_site_url()) ?>">
    <link rel="canonical" href="<?= e(full_site_url()) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= e(site_url('css/style.css')) ?>">
</head>
<body>
<header class="site-header">
    <nav class="navbar container">
        <a href="<?= e(site_url('index.php')) ?>" class="brand">
            <span class="brand-badge">CR</span>
            <span>
                <strong><?= e(APP_NAME) ?></strong>
                <small><?= e(APP_TAGLINE) ?></small>
            </span>
        </a>

        <button class="menu-toggle" id="menuToggle" aria-label="Open menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="nav-wrap" id="navWrap">
            <ul class="nav-links">
                <li><a class="<?= nav_active(['index.php']) ?>" href="<?= e(site_url('index.php')) ?>">Home</a></li>
                <li><a class="<?= nav_active(['menu.php']) ?>" href="<?= e(site_url('menu.php')) ?>">Menu</a></li>
                <li><a class="<?= nav_active(['about.php']) ?>" href="<?= e(site_url('about.php')) ?>">About</a></li>
                <li><a class="<?= nav_active(['contact.php']) ?>" href="<?= e(site_url('contact.php')) ?>">Contact</a></li>
                <li><a class="<?= nav_active(['order.php']) ?>" href="<?= e(site_url('order.php')) ?>">Order</a></li>
                <li><a class="<?= nav_active(['login.php']) ?>" href="<?= e(site_url('admin/login.php')) ?>">Admin</a></li>
            </ul>
            <div class="nav-actions">
                <a class="btn btn-outline" target="_blank" rel="noopener" href="<?= e(TELEGRAM_URL) ?>"><i class="fa-brands fa-telegram"></i> Daily Menu</a>
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <i class="fa-solid fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>
</header>

<?php if ($flashSuccess = get_flash('success')): ?>
    <div class="flash flash-success container"><?= e($flashSuccess) ?></div>
<?php endif; ?>
<?php if ($flashError = get_flash('error')): ?>
    <div class="flash flash-error container"><?= e($flashError) ?></div>
<?php endif; ?>

<main>
