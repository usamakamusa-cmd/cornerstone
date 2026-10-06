</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h3><?= e(APP_NAME) ?></h3>
            <p class="footer-tagline"><?= e(APP_TAGLINE) ?></p>
            <ul class="footer-list">
                <li><i class="fa-regular fa-clock"></i> Mon - Sun: 7:00 AM - 10:00 PM</li>
                <li><i class="fa-solid fa-location-dot"></i> Mafubira, Jinja, Uganda</li>
                <li><i class="fa-solid fa-phone"></i> <a href="tel:+256752064779">+256 752 064 779</a></li>
                <li><i class="fa-solid fa-envelope"></i> <a href="mailto:usamakamusa@gmail.com">usamakamusa@gmail.com</a></li>
            </ul>
        </div>

        <div>
            <h4>Quick Links</h4>
            <ul class="footer-links">
                <li><a href="<?= e(site_url('index.php')) ?>">Home</a></li>
                <li><a href="<?= e(site_url('menu.php')) ?>">Menu</a></li>
                <li><a href="<?= e(site_url('order.php')) ?>">Order Now</a></li>
                <li><a href="<?= e(site_url('about.php')) ?>">About Us</a></li>
                <li><a href="<?= e(site_url('contact.php')) ?>">Contact</a></li>
                <li><a target="_blank" rel="noopener" href="<?= e(TELEGRAM_URL) ?>">View Daily Menu Updates</a></li>
            </ul>
        </div>

        <div>
            <h4>Connect With Us</h4>
            <div class="social-links">
                <a href="<?= e(FACEBOOK_URL) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="<?= e(TIKTOK_URL) ?>" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                <a href="<?= e(TWITTER_URL) ?>" target="_blank" rel="noopener" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="<?= e(WHATSAPP_URL) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            </div>
            <div class="footer-actions">
                <a class="btn btn-primary btn-sm" href="mailto:usamakamusa@gmail.com"><i class="fa-solid fa-paper-plane"></i> Email</a>
                <a class="btn btn-outline btn-sm" href="tel:+256752064779"><i class="fa-solid fa-phone"></i> Call</a>
                <a class="btn btn-success btn-sm" target="_blank" rel="noopener" href="<?= e(WHATSAPP_URL) ?>"><i class="fa-brands fa-whatsapp"></i> Chat</a>
            </div>
        </div>

        <div>
            <h4>Developed by Kamusa Usama</h4>
            <div class="developer-card">
                <div class="developer-badge">KU</div>
                <div>
                    <p><strong>Kamusa Usama</strong></p>
                    <p><a href="mailto:usamakamusa@gmail.com">usamakamusa@gmail.com</a></p>
                    <p><a href="tel:+256752064779">+256 752 064 779</a></p>
                    <p><a target="_blank" rel="noopener" href="<?= e(WHATSAPP_URL) ?>">WhatsApp Inbox</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="container footer-bottom">
        <p>© 2026 <?= e(APP_NAME) ?>. All rights reserved.</p>
    </div>
</footer>

<a class="floating-whatsapp" target="_blank" rel="noopener" href="<?= e(WHATSAPP_URL) ?>?text=<?= rawurlencode('Hello Cornerstone Restaurant Mafubira, I would like to make an order.') ?>" aria-label="WhatsApp Chat">
    <i class="fa-brands fa-whatsapp"></i>
</a>
<button id="scrollTopBtn" class="scroll-top" aria-label="Scroll to top"><i class="fa-solid fa-arrow-up"></i></button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="<?= e(site_url('js/script.js')) ?>"></script>
</body>
</html>
