<?php
/**
 * Shared helper functions used across the application.
 */

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function site_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_PATH_URL . ($path !== '' ? '/' . $path : '');
}

function full_site_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . site_url($path);
}

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function currency_ugx(float $amount): string
{
    return 'UGX ' . number_format($amount, 0);
}

function current_page(): string
{
    return basename($_SERVER['PHP_SELF'] ?? '');
}

function nav_active(array $pages): string
{
    return in_array(current_page(), $pages, true) ? 'active' : '';
}

function set_flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $message;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function is_admin_logged_in(): bool
{
    return isset($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        set_flash('error', 'Please log in to continue.');
        redirect_to(site_url('admin/login.php'));
    }
}

function admin_exists(PDO $pdo): bool
{
    $count = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    return $count > 0;
}

function handle_image_upload(array $file): ?string
{
    if (empty($file['name'])) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed. Please try again.');
    }

    if (($file['size'] ?? 0) > 3 * 1024 * 1024) {
        throw new RuntimeException('Image size must be less than 3MB.');
    }

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowedMimeTypes[$mimeType])) {
        throw new RuntimeException('Only JPG, PNG, WEBP, or GIF images are allowed.');
    }

    $extension = $allowedMimeTypes[$mimeType];
    $uploadDir = dirname(__DIR__) . '/uploads';

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
        throw new RuntimeException('Upload directory could not be created.');
    }

    $safeFileName = 'food_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $target = $uploadDir . '/' . $safeFileName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Image could not be saved on the server.');
    }

    return 'uploads/' . $safeFileName;
}

function menu_image(?string $imagePath): string
{
    if (!$imagePath) {
        return 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80';
    }

    if (preg_match('#^https?://#i', $imagePath)) {
        return $imagePath;
    }

    return site_url($imagePath);
}

function whatsapp_item_link(string $itemName, int $quantity = 1): string
{
    $message = 'Hello, I want to order ' . $quantity . ' ' . $itemName . '.';
    return WHATSAPP_URL . '?text=' . rawurlencode($message);
}

function fetch_categories(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
    return $stmt->fetchAll();
}

function fetch_promotions(PDO $pdo, bool $onlyActive = true): array
{
    $sql = 'SELECT * FROM promotions';
    if ($onlyActive) {
        $sql .= ' WHERE is_active = 1';
    }
    $sql .= ' ORDER BY created_at DESC';

    return $pdo->query($sql)->fetchAll();
}

function fetch_testimonials(PDO $pdo, bool $onlyActive = true): array
{
    $sql = 'SELECT * FROM testimonials';
    if ($onlyActive) {
        $sql .= ' WHERE is_active = 1';
    }
    $sql .= ' ORDER BY created_at DESC';

    return $pdo->query($sql)->fetchAll();
}

function fetch_featured_items(PDO $pdo, int $limit = 6): array
{
    $stmt = $pdo->prepare('SELECT menu_items.*, categories.name AS category_name
        FROM menu_items
        LEFT JOIN categories ON categories.id = menu_items.category_id
        WHERE menu_items.is_available = 1
        ORDER BY menu_items.created_at DESC
        LIMIT :item_limit');
    $stmt->bindValue(':item_limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetch_menu_items(PDO $pdo, bool $onlyAvailable = false): array
{
    $sql = 'SELECT menu_items.*, categories.name AS category_name
        FROM menu_items
        LEFT JOIN categories ON categories.id = menu_items.category_id';

    if ($onlyAvailable) {
        $sql .= ' WHERE menu_items.is_available = 1';
    }

    $sql .= ' ORDER BY categories.name ASC, menu_items.name ASC';
    return $pdo->query($sql)->fetchAll();
}

function most_ordered_meals(PDO $pdo, int $limit = 5): array
{
    $stmt = $pdo->prepare('SELECT food_item_name, SUM(quantity) AS total_qty
        FROM customer_orders
        GROUP BY food_item_name
        ORDER BY total_qty DESC, food_item_name ASC
        LIMIT :item_limit');
    $stmt->bindValue(':item_limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function format_datetime(string $datetime): string
{
    return date('d M Y, h:i A', strtotime($datetime));
}
