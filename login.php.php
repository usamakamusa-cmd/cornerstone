<?php
require_once dirname(__DIR__) . '/config.php';

if (!admin_exists($pdo)) {
    redirect_to(site_url('admin/setup_admin.php'));
}

if (is_admin_logged_in()) {
    redirect_to(site_url('admin/dashboard.php'));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security validation failed. Please refresh and try again.';
    }

    if ($username === '' || $password === '') {
        $errors[] = 'Username and password are required.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            set_flash('success', 'Welcome back, ' . $admin['full_name'] . '.');
            redirect_to(site_url('admin/dashboard.php'));
        }

        $errors[] = 'Invalid login details.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= e(site_url('css/style.css')) ?>">
</head>
<body>
<div class="auth-wrapper container">
    <div class="auth-card">
        <span class="section-tag"><i class="fa-solid fa-lock"></i> Secure access</span>
        <h1>Admin Login</h1>
        <p class="muted">Manage menu items, food photos, pricing, promotions, and customer orders.</p>

        <?php if ($success = get_flash('success')): ?>
            <div class="flash flash-success"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="flash flash-error">
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input class="input" type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input class="input" type="password" id="password" name="password" required>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Login</button>
        </form>

        <p class="muted" style="margin-top: 1rem;">First installation? The system will redirect you to create the first admin account automatically.</p>
        <p><a href="<?= e(site_url('index.php')) ?>">← Back to website</a></p>
    </div>
</div>
</body>
</html>
