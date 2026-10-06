<?php
require_once __DIR__ . '/config.php';

$pageTitle = APP_NAME . ' | Contact';

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container section-heading">
        <div>
            <span class="section-tag"><i class="fa-solid fa-address-book"></i> Contact</span>
            <h1>Reach Cornerstone Restaurant Mafubira easily</h1>
            <p class="muted">Call, email, WhatsApp, or visit the restaurant in Mafubira, Jinja.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container contact-cards">
        <article class="contact-card">
            <span class="section-tag"><i class="fa-solid fa-phone"></i> Phone</span>
            <h3><a href="tel:+256752064779">+256 752 064 779</a></h3>
            <p class="muted">Tap to call the restaurant or developer support line.</p>
        </article>
        <article class="contact-card">
            <span class="section-tag"><i class="fa-solid fa-envelope"></i> Email</span>
            <h3><a href="mailto:usamakamusa@gmail.com">usamakamusa@gmail.com</a></h3>
            <p class="muted">Use mailto for quick email contact.</p>
        </article>
        <article class="contact-card">
            <span class="section-tag"><i class="fa-brands fa-whatsapp"></i> WhatsApp</span>
            <h3><a target="_blank" rel="noopener" href="<?= e(WHATSAPP_URL) ?>">Open Chat</a></h3>
            <p class="muted">Receive manual customer orders through WhatsApp.</p>
        </article>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <div class="info-card">
            <h2>Business Information</h2>
            <ul class="footer-list">
                <li><strong>Restaurant:</strong> <?= e(APP_NAME) ?></li>
                <li><strong>Tagline:</strong> <?= e(APP_TAGLINE) ?></li>
                <li><strong>Location:</strong> Mafubira, Jinja, Uganda</li>
                <li><strong>Opening Hours:</strong> Mon - Sun, 7:00 AM - 10:00 PM</li>
                <li><strong>Telegram:</strong> <a href="<?= e(TELEGRAM_URL) ?>" target="_blank" rel="noopener">View Daily Menu Updates</a></li>
            </ul>
            <div class="hero-actions">
                <a class="btn btn-success" target="_blank" rel="noopener" href="<?= e(WHATSAPP_URL) ?>?text=<?= rawurlencode('Hello, I would like to ask about your meals.') ?>">WhatsApp Inbox</a>
                <a class="btn btn-outline" target="_blank" rel="noopener" href="<?= e(TELEGRAM_URL) ?>">Telegram Channel</a>
            </div>
        </div>
        <div class="qr-box">
            <span class="section-tag"><i class="fa-solid fa-qrcode"></i> Menu QR Code</span>
            <h3>Share this with customers</h3>
            <div id="menuQrCode" data-url="<?= e(full_site_url('menu.php')) ?>"></div>
            <p class="muted">Scan to open the digital menu instantly from any smartphone browser.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <div class="map-frame">
            <iframe loading="lazy" src="https://www.google.com/maps?q=Mafubira,+Jinja,+Uganda&output=embed" allowfullscreen="" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="info-card">
            <h2>Need help managing the system?</h2>
            <p class="muted">This solution is beginner-friendly and suitable for deployment on XAMPP or live PHP hosting. Use the admin login page to manage menu items, food photos, prices, promotions, testimonials, and customer orders.</p>
            <a class="btn btn-primary" href="<?= e(site_url('admin/login.php')) ?>">Open Admin Login</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
