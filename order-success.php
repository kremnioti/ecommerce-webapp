<?php
require_once __DIR__ . '/includes/bootstrap.php';

$orderId = (int) ($_GET['id'] ?? 0);
$pageTitle = 'Order confirmed';

$order = null;
$items = [];

if ($orderId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if ($order) {
        $itemStmt = $pdo->prepare('SELECT product_name, unit_price, quantity FROM order_items WHERE order_id = ?');
        $itemStmt->execute([$orderId]);
        $items = $itemStmt->fetchAll();
    }
}

require __DIR__ . '/includes/header.php';
?>

<?php if (!$order): ?>
    <div class="alert alert-warning">Order not found.</div>
    <a href="index.php" class="btn btn-primary">Back to shop</a>
<?php else: ?>
    <div class="text-center py-4">
        <p class="text-success fs-1 mb-2">✓</p>
        <h1 class="h2">Thank you for your order!</h1>
        <p class="text-muted">Order #<?= (int) $order['id'] ?> — <?= format_money((float) $order['total']) ?></p>
    </div>
    <div class="card shadow-sm mx-auto" style="max-width: 520px;">
        <div class="card-body">
            <p><strong>Ship to:</strong> <?= e($order['customer_name']) ?></p>
            <p class="mb-0 small text-muted"><?= nl2br(e($order['shipping_address'])) ?></p>
            <hr>
            <ul class="list-unstyled mb-0">
                <?php foreach ($items as $item): ?>
                    <li class="d-flex justify-content-between small py-1">
                        <span><?= e($item['product_name']) ?> × <?= (int) $item['quantity'] ?></span>
                        <span><?= format_money((float) $item['unit_price'] * (int) $item['quantity']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-primary">Continue shopping</a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
