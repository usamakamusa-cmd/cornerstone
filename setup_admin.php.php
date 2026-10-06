<?php
require_once dirname(__DIR__) . '/config.php';

if (admin_exists($pdo)) {
    redirect_to(site_url('admin/login.php'));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security validation failed. Please refresh and try again.';
    }
    if ($username === '' || strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters long.';
    }
    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO admin_users (username, full_name, password_hash) VALUES (:username, :full_name, :password_hash)');
        $stmt->execute([
            'username' => $username,
            'full_name' => $fullName,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        set_flash('success', 'Admin account created successfully. Please log in.');
        redirect_to(site_url('admin/login.php'));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Admin | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= e(site_url('css/style.css')) ?>">
</head>
<body>
<div class="auth-wrapper container">
    <div class="auth-card">
        <span class="section-tag"><i class="fa-solid fa-user-shield"></i> First-time setup</span>
        <h1>Create Admin Account</h1>
        <p class="muted">Set up the first secure admin user for Cornerstone Restaurant Mafubira.</p>

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
                <label for="full_name">Full name</label>
                <input class="input" type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input class="input" type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input class="input" type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm password</label>
                <input class="input" type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Create Admin</button>
        </form>
    </div>
</div>
</body>
</html>
