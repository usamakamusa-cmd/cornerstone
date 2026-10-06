<?php
require_once __DIR__ . '/config.php';

$pageTitle = APP_NAME . ' | Order';
$menuItems = fetch_menu_items($pdo, true);
$selectedFoodId = isset($_GET['food']) ? (int) $_GET['food'] : 0;
$errors = [];
$formData = [
    'customer_name' => '',
    'phone_number' => '',
    'food_item' => (string) $selectedFoodId,
    'quantity' => '1',
    'delivery_location' => '',
    'payment_method' => 'Cash',
    'notes' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'customer_name' => trim($_POST['customer_name'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? ''),
        'food_item' => trim($_POST['food_item'] ?? ''),
        'quantity' => trim($_POST['quantity'] ?? '1'),
        'delivery_location' => trim($_POST['delivery_location'] ?? ''),
        'payment_method' => trim($_POST['payment_method'] ?? 'Cash'),
        'notes' => trim($_POST['notes'] ?? ''),
    ];

    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security check failed. Please refresh the page and try again.';
    }

    if ($formData['customer_name'] === '') {
        $errors[] = 'Customer name is required.';
    }

    if ($formData['phone_number'] === '') {
        $errors[] = 'Phone number is required.';
    }

    $quantity = max(1, (int) $formData['quantity']);
    if ($quantity < 1) {
        $errors[] = 'Quantity must be at least 1.';
    }

    if ($formData['delivery_location'] === '') {
        $errors[] = 'Delivery location is required.';
    }

    if (!in_array($formData['payment_method'], ['Cash', 'Mobile Money'], true)) {
        $errors[] = 'Please choose a valid payment method.';
    }

    $stmt = $pdo->prepare('SELECT id, name, price FROM menu_items WHERE id = :id AND is_available = 1 LIMIT 1');
    $stmt->execute(['id' => (int) $formData['food_item']]);
    $selectedItem = $stmt->fetch();

    if (!$selectedItem) {
        $errors[] = 'Please select an available food item.';
    }

    if (!$errors && $selectedItem) {
        $insert = $pdo->prepare('INSERT INTO customer_orders (customer_name, phone_number, menu_item_id, food_item_name, quantity, delivery_location, payment_method, notes)
            VALUES (:customer_name, :phone_number, :menu_item_id, :food_item_name, :quantity, :delivery_location, :payment_method, :notes)');

        $insert->execute([
            'customer_name' => $formData['customer_name'],
            'phone_number' => $formData['phone_number'],
            'menu_item_id' => (int) $selectedItem['id'],
            'food_item_name' => $selectedItem['name'],
            'quantity' => $quantity,
            'delivery_location' => $formData['delivery_location'],
            'payment_method' => $formData['payment_method'],
            'notes' => $formData['notes'],
        ]);

        $whatsAppMessage = "Hello, I want to order {$quantity} {$selectedItem['name']}.\n"
            . "Name: {$formData['customer_name']}\n"
            . "Phone: {$formData['phone_number']}\n"
            . "Delivery location: {$formData['delivery_location']}\n"
            . "Payment method: {$formData['payment_method']}";

        if ($formData['notes'] !== '') {
            $whatsAppMessage .= "\nExtra note: {$formData['notes']}";
        }

        redirect_to(WHATSAPP_URL . '?text=' . rawurlencode($whatsAppMessage));
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container section-heading">
        <div>
            <span class="section-tag"><i class="fa-solid fa-basket-shopping"></i> Place an Order</span>
            <h1>Simple order form with WhatsApp checkout</h1>
            <p class="muted">Submit the form, save the order in MySQL, then continue the conversation on WhatsApp.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <div class="order-card">
            <h2>Order Form</h2>
            <?php if ($errors): ?>
                <div class="flash flash-error">
                    <?php foreach ($errors as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(site_url('order.php')) ?>">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name">Customer name</label>
                        <input class="input" type="text" id="customer_name" name="customer_name" value="<?= e($formData['customer_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone number</label>
                        <input class="input" type="text" id="phone_number" name="phone_number" value="<?= e($formData['phone_number']) ?>" placeholder="e.g. 0752000000" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="food_item">Food item</label>
                        <select id="food_item" name="food_item" required>
                            <option value="">Select food item</option>
                            <?php foreach ($menuItems as $item): ?>
                                <option value="<?= (int) $item['id'] ?>" <?= (string) $item['id'] === (string) $formData['food_item'] ? 'selected' : '' ?>>
                                    <?= e($item['name']) ?> - <?= e(currency_ugx((float) $item['price'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input class="input" type="number" id="quantity" name="quantity" min="1" value="<?= e($formData['quantity']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="delivery_location">Delivery location</label>
                    <textarea id="delivery_location" name="delivery_location" required><?= e($formData['delivery_location']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="payment_method">Payment method</label>
                        <select id="payment_method" name="payment_method">
                            <option value="Cash" <?= $formData['payment_method'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
                            <option value="Mobile Money" <?= $formData['payment_method'] === 'Mobile Money' ? 'selected' : '' ?>>Mobile Money</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Extra note</label>
                        <input class="input" type="text" id="notes" name="notes" value="<?= e($formData['notes']) ?>" placeholder="Optional note for your order">
                    </div>
                </div>

                <button class="btn btn-primary btn-block" type="submit"><i class="fa-brands fa-whatsapp"></i> Save & Continue to WhatsApp</button>
            </form>
        </div>

        <div class="order-card">
            <span class="section-tag"><i class="fa-solid fa-receipt"></i> Order Preview</span>
            <h2 id="selectedFoodLabel">Choose meal</h2>
            <div class="order-summary-card">
                <p class="muted">After form submission, the system:</p>
                <ul class="footer-list">
                    <li>Saves the order details to the customer_orders table</li>
                    <li>Prepares a WhatsApp message automatically</li>
                    <li>Redirects the customer to WhatsApp chat manually</li>
                    <li>Keeps the workflow simple and beginner-friendly</li>
                </ul>
            </div>
            <div class="promo-banner" style="margin-top: 1rem;">
                <h3>Payment options</h3>
                <p>Customers can choose between Cash and Mobile Money directly from the form.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
