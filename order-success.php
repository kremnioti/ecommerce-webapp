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
    <div class="alert alert-warning alert-with-icon"><?= icon('help', 'icon-sm') ?><span>Order not found.</span></div>
    <a href="index.php" class="btn btn-primary btn-icon-left"><?= icon('storefront', 'icon-sm') ?>Back to shop</a>
<?php else: ?>
    <div class="text-center py-4">
        <div class="success-icon-circle"><?= icon('check_circle') ?></div>
        <h1 class="h2">Thank you for your order!</h1>
        <p class="text-muted d-flex align-items-center justify-content-center gap-1 flex-wrap">
            <?= icon('tag', 'icon-sm') ?>
            Order #<?= (int) $order['id'] ?> — <?= format_money((float) $order['total']) ?>
        </p>
    </div>
    <div class="card shadow-sm mx-auto" style="max-width: 520px;">
        <div class="card-body">
            <p class="d-flex align-items-center gap-1"><strong><?= icon('person', 'icon-sm') ?>Ship to:</strong> <?= e($order['customer_name']) ?></p>
            <p class="mb-0 small text-muted d-flex gap-2">
                <?= icon('location_on', 'icon-sm') ?>
                <span><?= nl2br(e($order['shipping_address'])) ?></span>
            </p>
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
        <a href="index.php" class="btn btn-primary btn-icon-left"><?= icon('shoppingmode', 'icon-sm') ?>Continue shopping</a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
