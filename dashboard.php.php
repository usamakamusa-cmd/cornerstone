<?php
require_once dirname(__DIR__) . '/config.php';
require_admin();

$redirectUrl = site_url('admin/dashboard.php');
$editMenuItem = null;

if (isset($_GET['edit_menu'])) {
    $editStmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = :id LIMIT 1');
    $editStmt->execute(['id' => (int) $_GET['edit_menu']]);
    $editMenuItem = $editStmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Security check failed. Please refresh the page and try again.');
        redirect_to($redirectUrl);
    }

    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'add_category':
                $name = trim($_POST['name'] ?? '');
                if ($name === '') {
                    throw new RuntimeException('Category name is required.');
                }
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
                $stmt = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (:name, :slug)');
                $stmt->execute(['name' => $name, 'slug' => $slug]);
                set_flash('success', 'Category added successfully.');
                break;

            case 'delete_category':
                $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                set_flash('success', 'Category deleted successfully.');
                break;

            case 'add_promotion':
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                if ($title === '' || $description === '') {
                    throw new RuntimeException('Promotion title and description are required.');
                }
                $stmt = $pdo->prepare('INSERT INTO promotions (title, description, is_active) VALUES (:title, :description, :is_active)');
                $stmt->execute(['title' => $title, 'description' => $description, 'is_active' => $isActive]);
                set_flash('success', 'Promotion saved successfully.');
                break;

            case 'toggle_promotion':
                $stmt = $pdo->prepare('UPDATE promotions SET is_active = NOT is_active WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                set_flash('success', 'Promotion status updated.');
                break;

            case 'delete_promotion':
                $stmt = $pdo->prepare('DELETE FROM promotions WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                set_flash('success', 'Promotion deleted.');
                break;

            case 'add_testimonial':
                $customerName = trim($_POST['customer_name'] ?? '');
                $location = trim($_POST['location'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                if ($customerName === '' || $message === '') {
                    throw new RuntimeException('Customer name and testimonial message are required.');
                }
                $stmt = $pdo->prepare('INSERT INTO testimonials (customer_name, location, message, is_active) VALUES (:customer_name, :location, :message, :is_active)');
                $stmt->execute([
                    'customer_name' => $customerName,
                    'location' => $location,
                    'message' => $message,
                    'is_active' => $isActive,
                ]);
                set_flash('success', 'Testimonial added successfully.');
                break;

            case 'toggle_testimonial':
                $stmt = $pdo->prepare('UPDATE testimonials SET is_active = NOT is_active WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                set_flash('success', 'Testimonial status updated.');
                break;

            case 'delete_testimonial':
                $stmt = $pdo->prepare('DELETE FROM testimonials WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                set_flash('success', 'Testimonial deleted.');
                break;

            case 'add_menu_item':
            case 'update_menu_item':
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $categoryId = (int) ($_POST['category_id'] ?? 0);
                $price = (float) ($_POST['price'] ?? 0);
                $isAvailable = isset($_POST['is_available']) ? 1 : 0;
                $imageUrl = trim($_POST['image_url'] ?? '');
                $itemId = (int) ($_POST['id'] ?? 0);
                $existingImage = trim($_POST['existing_image'] ?? '');

                if ($name === '' || $description === '' || $categoryId < 1 || $price <= 0) {
                    throw new RuntimeException('Name, description, category, and a valid price are required.');
                }

                $uploadedImage = null;
                if (!empty($_FILES['image_file']['name'])) {
                    $uploadedImage = handle_image_upload($_FILES['image_file']);
                }

                $finalImage = $uploadedImage ?: ($imageUrl !== '' ? $imageUrl : $existingImage);

                if ($action === 'add_menu_item') {
                    $stmt = $pdo->prepare('INSERT INTO menu_items (category_id, name, description, price, image, is_available)
                        VALUES (:category_id, :name, :description, :price, :image, :is_available)');
                    $stmt->execute([
                        'category_id' => $categoryId,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'image' => $finalImage,
                        'is_available' => $isAvailable,
                    ]);
                    set_flash('success', 'Menu item added successfully.');
                } else {
                    $stmt = $pdo->prepare('UPDATE menu_items
                        SET category_id = :category_id, name = :name, description = :description, price = :price, image = :image, is_available = :is_available
                        WHERE id = :id');
                    $stmt->execute([
                        'category_id' => $categoryId,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'image' => $finalImage,
                        'is_available' => $isAvailable,
                        'id' => $itemId,
                    ]);
                    set_flash('success', 'Menu item updated successfully.');
                }
                break;

            case 'toggle_menu_item':
                $stmt = $pdo->prepare('UPDATE menu_items SET is_available = NOT is_available WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                set_flash('success', 'Availability updated successfully.');
                break;

            case 'delete_menu_item':
                $stmt = $pdo->prepare('DELETE FROM menu_items WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
                set_flash('success', 'Menu item deleted successfully.');
                break;

            case 'update_order_status':
                $status = trim($_POST['order_status'] ?? 'Pending');
                if (!in_array($status, ['Pending', 'Confirmed', 'Preparing', 'Delivered', 'Cancelled'], true)) {
                    throw new RuntimeException('Invalid order status selected.');
                }
                $stmt = $pdo->prepare('UPDATE customer_orders SET order_status = :status WHERE id = :id');
                $stmt->execute([
                    'status' => $status,
                    'id' => (int) ($_POST['id'] ?? 0),
                ]);
                set_flash('success', 'Order status updated.');
                break;
        }
    } catch (Throwable $exception) {
        set_flash('error', $exception->getMessage());
    }

    redirect_to($redirectUrl);
}

$totalMenuItems = (int) $pdo->query('SELECT COUNT(*) FROM menu_items')->fetchColumn();
$availableItems = (int) $pdo->query('SELECT COUNT(*) FROM menu_items WHERE is_available = 1')->fetchColumn();
$totalOrders = (int) $pdo->query('SELECT COUNT(*) FROM customer_orders')->fetchColumn();
$totalCategories = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$categories = fetch_categories($pdo);
$promotions = fetch_promotions($pdo, false);
$testimonials = fetch_testimonials($pdo, false);
$menuItems = fetch_menu_items($pdo, false);
$mostOrdered = most_ordered_meals($pdo, 5);
$orderStmt = $pdo->query('SELECT * FROM customer_orders ORDER BY created_at DESC');
$orders = $orderStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= e(site_url('css/style.css')) ?>">
</head>
<body>
<header class="site-header">
    <nav class="navbar container">
        <a href="<?= e(site_url('admin/dashboard.php')) ?>" class="brand">
            <span class="brand-badge">CR</span>
            <span>
                <strong>Admin Dashboard</strong>
                <small><?= e(APP_NAME) ?></small>
            </span>
        </a>
        <div class="nav-actions">
            <span class="pill"><i class="fa-solid fa-user"></i> <?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
            <a class="btn btn-outline btn-sm" href="<?= e(site_url('index.php')) ?>">View Site</a>
            <a class="btn btn-danger btn-sm" href="<?= e(site_url('admin/logout.php')) ?>">Logout</a>
        </div>
    </nav>
</header>

<div class="container">
    <?php if ($flashSuccess = get_flash('success')): ?>
        <div class="flash flash-success" style="margin-top: 1.2rem;"><?= e($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError = get_flash('error')): ?>
        <div class="flash flash-error" style="margin-top: 1.2rem;"><?= e($flashError) ?></div>
    <?php endif; ?>
</div>

<div class="container dashboard-shell">
    <aside class="sidebar card">
        <a class="active" href="#overview">Overview</a>
        <a href="#menu-items">Menu Items</a>
        <a href="#categories">Categories</a>
        <a href="#promotions">Promotions</a>
        <a href="#testimonials">Testimonials</a>
        <a href="#orders">Orders</a>
    </aside>

    <section>
        <div id="overview" class="admin-section">
            <div class="section-heading">
                <div>
                    <span class="section-tag"><i class="fa-solid fa-chart-line"></i> Dashboard Statistics</span>
                    <h1>Restaurant management overview</h1>
                    <p class="muted">Monitor menu items, orders, and top-performing meals from one place.</p>
                </div>
            </div>

            <div class="stats-grid">
                <article class="dashboard-card text-center"><h3><?= $totalMenuItems ?></h3><p class="muted">Total menu items</p></article>
                <article class="dashboard-card text-center"><h3><?= $availableItems ?></h3><p class="muted">Available items</p></article>
                <article class="dashboard-card text-center"><h3><?= $totalOrders ?></h3><p class="muted">Total orders</p></article>
                <article class="dashboard-card text-center"><h3><?= $totalCategories ?></h3><p class="muted">Categories</p></article>
            </div>

            <div class="grid-2" style="margin-top: 1.5rem;">
                <div class="table-card">
                    <h3>Most Ordered Meals</h3>
                    <?php if ($mostOrdered): ?>
                        <ul class="footer-list">
                            <?php foreach ($mostOrdered as $meal): ?>
                                <li><strong><?= e($meal['food_item_name']) ?></strong> — <?= (int) $meal['total_qty'] ?> orders</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">No orders yet.</div>
                    <?php endif; ?>
                </div>
                <div class="table-card">
                    <h3>System Tips</h3>
                    <ul class="footer-list">
                        <li>Upload food photos or paste direct image URLs</li>
                        <li>Toggle availability to show or hide dishes instantly</li>
                        <li>Use promotions for daily specials and lunch offers</li>
                        <li>Review customer orders and update their status</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="menu-items" class="admin-section">
            <div class="section-heading">
                <div>
                    <span class="section-tag"><i class="fa-solid fa-bowl-food"></i> Menu Management</span>
                    <h2><?= $editMenuItem ? 'Edit Menu Item' : 'Add Menu Item' ?></h2>
                </div>
            </div>

            <div class="table-card">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="<?= $editMenuItem ? 'update_menu_item' : 'add_menu_item' ?>">
                    <input type="hidden" name="id" value="<?= (int) ($editMenuItem['id'] ?? 0) ?>">
                    <input type="hidden" name="existing_image" value="<?= e($editMenuItem['image'] ?? '') ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Food name</label>
                            <input class="input" type="text" id="name" name="name" value="<?= e($editMenuItem['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['id'] ?>" <?= (int) ($editMenuItem['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Price (UGX)</label>
                            <input class="input" type="number" step="0.01" min="0" id="price" name="price" value="<?= e((string) ($editMenuItem['price'] ?? '')) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?= e($editMenuItem['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="image_url">Image URL</label>
                            <input class="input" type="url" id="image_url" name="image_url" value="<?= e($editMenuItem['image'] ?? '') ?>" placeholder="Optional remote image URL">
                        </div>
                        <div class="form-group">
                            <label for="image_file">Upload food image</label>
                            <input class="input" type="file" id="image_file" name="image_file" accept="image/*">
                        </div>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_available" <?= (int) ($editMenuItem['is_available'] ?? 1) === 1 ? 'checked' : '' ?>> Item available</label>
                    </div>
                    <div class="hero-actions">
                        <button class="btn btn-primary" type="submit"><?= $editMenuItem ? 'Update Item' : 'Add Item' ?></button>
                        <?php if ($editMenuItem): ?>
                            <a class="btn btn-outline" href="<?= e(site_url('admin/dashboard.php#menu-items')) ?>">Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="table-card" style="margin-top: 1.5rem;">
                <h3>All Menu Items</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menuItems as $item): ?>
                                <tr>
                                    <td><img src="<?= e(menu_image($item['image'])) ?>" alt="<?= e($item['name']) ?>" style="width:70px;height:70px;object-fit:cover;border-radius:16px;"></td>
                                    <td>
                                        <strong><?= e($item['name']) ?></strong><br>
                                        <span class="muted"><?= e($item['description']) ?></span>
                                    </td>
                                    <td><?= e($item['category_name'] ?? 'N/A') ?></td>
                                    <td><?= e(currency_ugx((float) $item['price'])) ?></td>
                                    <td><span class="status-pill <?= $item['is_available'] ? 'status-available' : 'status-unavailable' ?>"><?= $item['is_available'] ? 'Available' : 'Unavailable' ?></span></td>
                                    <td>
                                        <div class="hero-actions">
                                            <a class="btn btn-outline btn-sm" href="<?= e(site_url('admin/dashboard.php?edit_menu=' . (int) $item['id'] . '#menu-items')) ?>">Edit</a>
                                            <form method="post">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="action" value="toggle_menu_item">
                                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                                <button class="btn btn-outline btn-sm" type="submit">Toggle</button>
                                            </form>
                                            <form method="post" onsubmit="return confirm('Delete this menu item?');">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="action" value="delete_menu_item">
                                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="categories" class="admin-section">
            <div class="section-heading">
                <div>
                    <span class="section-tag"><i class="fa-solid fa-tags"></i> Categories</span>
                    <h2>Manage food categories</h2>
                </div>
            </div>
            <div class="grid-2">
                <div class="table-card">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="add_category">
                        <div class="form-group">
                            <label for="category_name">Category name</label>
                            <input class="input" type="text" id="category_name" name="name" placeholder="Breakfast, Lunch, Dinner..." required>
                        </div>
                        <button class="btn btn-primary" type="submit">Add Category</button>
                    </form>
                </div>
                <div class="table-card">
                    <h3>Current Categories</h3>
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Name</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= e($category['name']) ?></td>
                                        <td>
                                            <form method="post" onsubmit="return confirm('Delete this category? Menu items will remain but become uncategorized.');">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="action" value="delete_category">
                                                <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="promotions" class="admin-section">
            <div class="section-heading">
                <div>
                    <span class="section-tag"><i class="fa-solid fa-bullhorn"></i> Promotions</span>
                    <h2>Manage banners and daily specials</h2>
                </div>
            </div>
            <div class="grid-2">
                <div class="table-card">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="add_promotion">
                        <div class="form-group">
                            <label for="promo_title">Promotion title</label>
                            <input class="input" type="text" id="promo_title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="promo_description">Description</label>
                            <textarea id="promo_description" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_active" checked> Active banner</label>
                        </div>
                        <button class="btn btn-primary" type="submit">Save Promotion</button>
                    </form>
                </div>
                <div class="table-card">
                    <h3>Promotion List</h3>
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Title</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($promotions as $promotion): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($promotion['title']) ?></strong><br>
                                            <span class="muted"><?= e($promotion['description']) ?></span>
                                        </td>
                                        <td><span class="status-pill <?= $promotion['is_active'] ? 'status-available' : 'status-unavailable' ?>"><?= $promotion['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                                        <td>
                                            <div class="hero-actions">
                                                <form method="post">
                                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                    <input type="hidden" name="action" value="toggle_promotion">
                                                    <input type="hidden" name="id" value="<?= (int) $promotion['id'] ?>">
                                                    <button class="btn btn-outline btn-sm" type="submit">Toggle</button>
                                                </form>
                                                <form method="post" onsubmit="return confirm('Delete this promotion?');">
                                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                    <input type="hidden" name="action" value="delete_promotion">
                                                    <input type="hidden" name="id" value="<?= (int) $promotion['id'] ?>">
                                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="testimonials" class="admin-section">
            <div class="section-heading">
                <div>
                    <span class="section-tag"><i class="fa-solid fa-comments"></i> Testimonials</span>
                    <h2>Manage customer reviews</h2>
                </div>
            </div>
            <div class="grid-2">
                <div class="table-card">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="add_testimonial">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customer_name">Customer name</label>
                                <input class="input" type="text" id="customer_name" name="customer_name" required>
                            </div>
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input class="input" type="text" id="location" name="location" placeholder="Jinja, Uganda">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Testimonial message</label>
                            <textarea id="message" name="message" required></textarea>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_active" checked> Show on homepage</label>
                        </div>
                        <button class="btn btn-primary" type="submit">Add Testimonial</button>
                    </form>
                </div>
                <div class="table-card">
                    <h3>Testimonials List</h3>
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Customer</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($testimonials as $testimonial): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($testimonial['customer_name']) ?></strong><br>
                                            <span class="muted"><?= e($testimonial['message']) ?></span>
                                        </td>
                                        <td><span class="status-pill <?= $testimonial['is_active'] ? 'status-available' : 'status-unavailable' ?>"><?= $testimonial['is_active'] ? 'Visible' : 'Hidden' ?></span></td>
                                        <td>
                                            <div class="hero-actions">
                                                <form method="post">
                                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                    <input type="hidden" name="action" value="toggle_testimonial">
                                                    <input type="hidden" name="id" value="<?= (int) $testimonial['id'] ?>">
                                                    <button class="btn btn-outline btn-sm" type="submit">Toggle</button>
                                                </form>
                                                <form method="post" onsubmit="return confirm('Delete this testimonial?');">
                                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                    <input type="hidden" name="action" value="delete_testimonial">
                                                    <input type="hidden" name="id" value="<?= (int) $testimonial['id'] ?>">
                                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="orders" class="admin-section">
            <div class="section-heading">
                <div>
                    <span class="section-tag"><i class="fa-solid fa-receipt"></i> Customer Orders</span>
                    <h2>View and manage incoming orders</h2>
                </div>
            </div>
            <div class="table-card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Meal</th>
                                <th>Quantity</th>
                                <th>Location</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($order['customer_name']) ?></strong><br>
                                            <a href="tel:<?= e($order['phone_number']) ?>"><?= e($order['phone_number']) ?></a>
                                        </td>
                                        <td>
                                            <strong><?= e($order['food_item_name']) ?></strong>
                                            <?php if (!empty($order['notes'])): ?>
                                                <br><span class="muted">Note: <?= e($order['notes']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= (int) $order['quantity'] ?></td>
                                        <td><?= e($order['delivery_location']) ?></td>
                                        <td><?= e($order['payment_method']) ?></td>
                                        <td><span class="order-badge"><?= e($order['order_status']) ?></span></td>
                                        <td><?= e(format_datetime($order['created_at'])) ?></td>
                                        <td>
                                            <form method="post">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="action" value="update_order_status">
                                                <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                                                <select name="order_status" style="margin-bottom:0.5rem;">
                                                    <?php foreach (['Pending', 'Confirmed', 'Preparing', 'Delivered', 'Cancelled'] as $status): ?>
                                                        <option value="<?= e($status) ?>" <?= $order['order_status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button class="btn btn-outline btn-sm" type="submit">Save</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="empty-state">No customer orders have been placed yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?= e(site_url('js/script.js')) ?>"></script>
</body>
</html>
