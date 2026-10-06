<?php
require_once __DIR__ . '/config.php';

$pageTitle = APP_NAME . ' | Home';
$featuredItems = fetch_featured_items($pdo, 6);
$promotions = fetch_promotions($pdo, true);
$testimonials = fetch_testimonials($pdo, true);
$galleryItems = fetch_featured_items($pdo, 3);

include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-copy">
            <span class="section-tag"><i class="fa-solid fa-utensils"></i> Premium Digital Menu Board</span>
            <h1>Fresh Ugandan meals served fast, beautifully, and with heart.</h1>
            <p>
                Welcome to <?= e(APP_NAME) ?> — your modern neighborhood restaurant in Mafubira.
                Explore our daily menu online, send your order through WhatsApp, and enjoy a premium mobile-friendly experience without downloading any app.
            </p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?= e(site_url('menu.php')) ?>"><i class="fa-solid fa-bowl-food"></i> Explore Menu</a>
                <a class="btn btn-outline" href="<?= e(site_url('order.php')) ?>"><i class="fa-solid fa-cart-shopping"></i> Order Now</a>
                <a class="btn btn-success" target="_blank" rel="noopener" href="<?= e(TELEGRAM_URL) ?>"><i class="fa-brands fa-telegram"></i> Daily Menu Updates</a>
            </div>
        </div>

        <div class="hero-card">
            <h3>Why customers love us</h3>
            <div class="hero-card-grid">
                <div class="hero-stat"><strong>Fast</strong><span>Easy WhatsApp ordering</span></div>
                <div class="hero-stat"><strong>Fresh</strong><span>Daily updated menu items</span></div>
                <div class="hero-stat"><strong>Local</strong><span>Ugandan-friendly pricing in UGX</span></div>
                <div class="hero-stat"><strong>Simple</strong><span>No app download required</span></div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-tag"><i class="fa-solid fa-fire"></i> Featured Meals</span>
                <h2>Popular dishes prepared with great taste</h2>
                <p class="muted">Dynamic food cards pulled from the MySQL menu database.</p>
            </div>
            <a class="btn btn-outline btn-sm" href="<?= e(site_url('menu.php')) ?>">View Full Menu</a>
        </div>

        <div class="menu-grid">
            <?php if ($featuredItems): ?>
                <?php foreach ($featuredItems as $item): ?>
                    <article class="menu-card">
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
                                    <i class="fa-solid fa-circle"></i>
                                    <?= $item['is_available'] ? 'Available' : 'Unavailable' ?>
                                </span>
                            </div>
                            <div class="hero-actions">
                                <a class="btn btn-primary btn-sm" href="<?= e(site_url('order.php?food=' . (int) $item['id'])) ?>">Order</a>
                                <a class="btn btn-success btn-sm" target="_blank" rel="noopener" href="<?= e(whatsapp_item_link($item['name'])) ?>">WhatsApp</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card empty-state" style="grid-column: 1/-1;">No featured meals yet. Add menu items from the admin dashboard.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-tag"><i class="fa-solid fa-bullhorn"></i> Promotions & Daily Specials</span>
                <h2>Today’s exciting offers</h2>
            </div>
        </div>
        <div class="promo-stack">
            <?php if ($promotions): ?>
                <?php foreach ($promotions as $promotion): ?>
                    <div class="promo-banner">
                        <div class="card-top">
                            <div>
                                <h3><?= e($promotion['title']) ?></h3>
                                <p><?= e($promotion['description']) ?></p>
                            </div>
                            <span class="pill" style="background: rgba(255,255,255,0.18); color: #fff;">Active Offer</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="promo-banner">
                    <h3>Daily Specials Available</h3>
                    <p>Use the admin dashboard to publish today’s promotions, lunch offers, breakfast combos, and drinks specials.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container feature-grid">
        <div class="info-card">
            <span class="section-tag"><i class="fa-solid fa-mobile-screen-button"></i> Mobile Ready</span>
            <h3>Fast on smartphones</h3>
            <p class="muted">Customers can browse the restaurant menu and place orders from any phone browser with a clean, lightweight experience.</p>
        </div>
        <div class="info-card">
            <span class="section-tag"><i class="fa-brands fa-whatsapp"></i> Manual WhatsApp Orders</span>
            <h3>Simple and familiar</h3>
            <p class="muted">Orders are saved in MySQL first, then customers are redirected to WhatsApp with a ready-made order message.</p>
        </div>
        <div class="info-card">
            <span class="section-tag"><i class="fa-solid fa-gear"></i> Easy to Manage</span>
            <h3>Beginner-friendly admin tools</h3>
            <p class="muted">Update menu photos, prices, availability, promotions, categories, and testimonials without touching code.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-tag"><i class="fa-solid fa-quote-left"></i> Testimonials</span>
                <h2>What our guests say</h2>
            </div>
        </div>

        <div class="testimonial-slider">
            <div class="testimonial-track">
                <?php if ($testimonials): ?>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <article class="testimonial-card">
                            <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                            <p>“<?= e($testimonial['message']) ?>”</p>
                            <strong><?= e($testimonial['customer_name']) ?></strong>
                            <div class="muted"><?= e($testimonial['location']) ?></div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <article class="testimonial-card"><p>“Amazing local meals and a very easy ordering process.”</p><strong>Happy Customer</strong></article>
                    <article class="testimonial-card"><p>“The website looks premium and works perfectly on my phone.”</p><strong>Regular Guest</strong></article>
                    <article class="testimonial-card"><p>“Fast service, fresh meals, and smooth WhatsApp ordering.”</p><strong>Office Team</strong></article>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <div class="info-card">
            <span class="section-tag"><i class="fa-regular fa-clock"></i> Opening Hours</span>
            <h2>Visit us any day</h2>
            <ul class="footer-list">
                <li>Monday - Friday: 7:00 AM - 10:00 PM</li>
                <li>Saturday: 8:00 AM - 10:00 PM</li>
                <li>Sunday: 8:00 AM - 9:30 PM</li>
                <li>Breakfast, lunch, dinner, drinks, and snacks served daily</li>
            </ul>
        </div>

        <div class="qr-box">
            <span class="section-tag"><i class="fa-solid fa-qrcode"></i> Scan Menu QR</span>
            <h3>Open the digital menu instantly</h3>
            <div id="menuQrCode" data-url="<?= e(full_site_url('menu.php')) ?>"></div>
            <p class="muted">Perfect for printing on tables, flyers, or the restaurant counter.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-tag"><i class="fa-regular fa-image"></i> Food Gallery</span>
                <h2>Responsive image gallery</h2>
            </div>
        </div>
        <div class="gallery-grid">
            <?php if ($galleryItems): ?>
                <?php foreach ($galleryItems as $galleryItem): ?>
                    <article class="gallery-card">
                        <img src="<?= e(menu_image($galleryItem['image'])) ?>" alt="<?= e($galleryItem['name']) ?>" loading="lazy">
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <article class="gallery-card"><img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=80" alt="Restaurant dining area" loading="lazy"></article>
                <article class="gallery-card"><img src="https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=900&q=80" alt="Chef plating food" loading="lazy"></article>
                <article class="gallery-card"><img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=900&q=80" alt="Fresh meal served" loading="lazy"></article>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <div class="map-frame">
            <iframe loading="lazy" src="https://www.google.com/maps?q=Mafubira,+Jinja,+Uganda&output=embed" allowfullscreen="" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="info-card">
            <span class="section-tag"><i class="fa-solid fa-location-dot"></i> Find Us</span>
            <h2>Cornerstone Restaurant Mafubira</h2>
            <p class="muted">Located in Mafubira, Jinja, Uganda. Ideal for dine-in, takeaway, and local delivery coordination through WhatsApp.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?= e(site_url('contact.php')) ?>">Contact Us</a>
                <a class="btn btn-outline" target="_blank" rel="noopener" href="<?= e(WHATSAPP_URL) ?>?text=<?= rawurlencode('Hello, please share today\'s menu.') ?>">Ask for Today’s Menu</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
