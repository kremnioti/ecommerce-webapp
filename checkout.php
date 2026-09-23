<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Checkout';

// Rebuild cart lines (same logic as cart page)
$cart = cart_get();
$lines = [];
$subtotal = 0.0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $p) {
        $qty = min((int) ($cart[$p['id']] ?? 0), (int) $p['stock']);
        if ($qty <= 0) {
            continue;
        }
        $lineTotal = (float) $p['price'] * $qty;
        $subtotal += $lineTotal;
        $lines[] = ['product' => $p, 'quantity' => $qty, 'line_total' => $lineTotal];
    }
}

if (empty($lines)) {
    header('Location: cart.php');
    exit;
}

$errors = [];
$form = [
    'name' => '',
    'email' => '',
    'address' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['name'] = trim($_POST['name'] ?? '');
    $form['email'] = trim($_POST['email'] ?? '');
    $form['address'] = trim($_POST['address'] ?? '');

    if ($form['name'] === '') {
        $errors[] = 'Name is required.';
    }
    if ($form['email'] === '' || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }
    if ($form['address'] === '') {
        $errors[] = 'Shipping address is required.';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $orderStmt = $pdo->prepare(
                'INSERT INTO orders (customer_name, customer_email, shipping_address, total) VALUES (?, ?, ?, ?)'
            );
            $orderStmt->execute([$form['name'], $form['email'], $form['address'], $subtotal]);
            $orderId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity) VALUES (?, ?, ?, ?, ?)'
            );
            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');

            foreach ($lines as $line) {
                $p = $line['product'];
                $qty = $line['quantity'];
                $itemStmt->execute([$orderId, $p['id'], $p['name'], $p['price'], $qty]);
                $stockStmt->execute([$qty, $p['id'], $qty]);
                if ($stockStmt->rowCount() === 0) {
                    throw new RuntimeException('Insufficient stock for ' . $p['name']);
                }
            }

            $pdo->commit();
            $_SESSION['cart'] = [];
            header('Location: order-success.php?id=' . $orderId);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Could not place order. Please try again or update your cart.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<h1 class="h2 mb-4">Checkout</h1>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-danger"><?= e($err) ?></div>
<?php endforeach; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 card-title">Shipping details</h2>
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full name</label>
                        <input type="text" class="form-control" id="name" name="name" required
                               value="<?= e($form['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               value="<?= e($form['email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Shipping address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?= e($form['address']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Place order</button>
                    <a href="cart.php" class="btn btn-link">Back to cart</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 card-title">Order summary</h2>
                <ul class="list-group list-group-flush mb-3">
                    <?php foreach ($lines as $line): ?>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span><?= e($line['product']['name']) ?> × <?= (int) $line['quantity'] ?></span>
                            <span><?= format_money($line['line_total']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="d-flex justify-content-between fs-5 mb-0">
                    <span>Total</span>
                    <strong><?= format_money($subtotal) ?></strong>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
