<?php
require_once __DIR__ . '/config.php';

$pageTitle = APP_NAME . ' | About';
$galleryItems = fetch_featured_items($pdo, 6);

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container section-heading">
        <div>
            <span class="section-tag"><i class="fa-solid fa-store"></i> About Us</span>
            <h1>Modern restaurant service rooted in local hospitality</h1>
            <p class="muted">We combine fresh meals, friendly service, and a premium digital experience for customers across Mafubira and Jinja.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <div class="info-card">
            <h2>Our Story</h2>
            <p class="muted">Cornerstone Restaurant Mafubira was designed to serve busy individuals, families, and workers who want quality meals and an easy way to order. This website gives customers quick access to daily menu items, meal photos, promotions, and WhatsApp ordering in a clean mobile-first format.</p>
            <p class="muted">The platform is ideal for Ugandan restaurants because it is fast, beginner-friendly to manage, and works well with familiar tools like WhatsApp and Mobile Money.</p>
        </div>
        <div class="info-card">
            <h2>What Makes Us Special</h2>
            <ul class="footer-list">
                <li>Fresh breakfast, lunch, dinner, drinks, and snacks</li>
                <li>Easy smartphone browsing without any app download</li>
                <li>Quick manual order handling through WhatsApp</li>
                <li>Admin-controlled daily menu and promotions dashboard</li>
                <li>Clean, modern, and premium website experience</li>
            </ul>
        </div>
    </div>
</section>

<section class="section">
    <div class="container stats-grid">
        <div class="info-card text-center"><h3>100%</h3><p class="muted">Responsive design</p></div>
        <div class="info-card text-center"><h3>5</h3><p class="muted">Main food categories</p></div>
        <div class="info-card text-center"><h3>24/7</h3><p class="muted">Online menu visibility</p></div>
        <div class="info-card text-center"><h3>UGX</h3><p class="muted">Local pricing display</p></div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-tag"><i class="fa-regular fa-image"></i> Restaurant Gallery</span>
                <h2>Premium food presentation</h2>
            </div>
        </div>
        <div class="gallery-grid">
            <?php if ($galleryItems): ?>
                <?php foreach ($galleryItems as $item): ?>
                    <article class="gallery-card">
                        <img src="<?= e(menu_image($item['image'])) ?>" alt="<?= e($item['name']) ?>" loading="lazy">
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <article class="gallery-card"><img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=900&q=80" alt="Breakfast meal" loading="lazy"></article>
                <article class="gallery-card"><img src="https://images.unsplash.com/photo-1529042410759-befb1204b468?auto=format&fit=crop&w=900&q=80" alt="Lunch meal" loading="lazy"></article>
                <article class="gallery-card"><img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&w=900&q=80" alt="Dinner meal" loading="lazy"></article>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-3">
        <div class="info-card">
            <span class="section-tag"><i class="fa-solid fa-seedling"></i> Fresh Ingredients</span>
            <p class="muted">Every plate is presented with care and prepared for quality, taste, and consistency.</p>
        </div>
        <div class="info-card">
            <span class="section-tag"><i class="fa-solid fa-motorcycle"></i> Ready for Delivery Coordination</span>
            <p class="muted">Customers can send delivery location details directly from the order page before moving to WhatsApp.</p>
        </div>
        <div class="info-card">
            <span class="section-tag"><i class="fa-solid fa-heart"></i> Built for Customer Delight</span>
            <p class="muted">Smooth design, fast loading, and beautiful food cards make the experience feel premium from the first click.</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
