<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Shopping Cart';

// Apply cart changes before we render lines
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] ?? [] as $productId => $qty) {
        cart_set_quantity((int) $productId, (int) $qty);
    }
    header('Location: cart.php');
    exit;
}

$cart = cart_get();
$lines = [];
$subtotal = 0.0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, stock, image_url FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productsById = [];
    foreach ($stmt->fetchAll() as $row) {
        $productsById[$row['id']] = $row;
    }

    foreach ($cart as $productId => $qty) {
        if (!isset($productsById[$productId])) {
            cart_set_quantity((int) $productId, 0);
            continue;
        }
        $p = $productsById[$productId];
        $qty = min((int) $qty, (int) $p['stock']);
        cart_set_quantity((int) $productId, $qty);
        $lineTotal = (float) $p['price'] * $qty;
        $subtotal += $lineTotal;
        $lines[] = [
            'product' => $p,
            'quantity' => $qty,
            'line_total' => $lineTotal,
        ];
    }
}

require __DIR__ . '/includes/header.php';
?>

<h1 class="h2 mb-4 page-title"><?= icon('shopping_cart') ?><span>Your cart</span></h1>

<?php if (empty($lines)): ?>
    <div class="alert alert-info alert-with-icon">
        <?= icon('remove_shopping_cart', 'icon-sm') ?>
        <span>Your cart is empty. <a href="index.php">Continue shopping</a></span>
    </div>
<?php else: ?>
    <form method="post" id="cart-update-form">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th><?= icon('package_2', 'icon-sm icon-inline-text') ?>Product</th>
                    <th class="text-end"><?= icon('sell', 'icon-sm icon-inline-text') ?>Price</th>
                    <th style="width: 120px;"><?= icon('pin', 'icon-sm icon-inline-text') ?>Qty</th>
                    <th class="text-end"><?= icon('calculate', 'icon-sm icon-inline-text') ?>Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($lines as $line): ?>
                    <?php $p = $line['product']; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= e($p['image_url']) ?>" alt="" class="cart-line-thumb">
                                <span><?= e($p['name']) ?></span>
                            </div>
                        </td>
                        <td class="text-end"><?= format_money((float) $p['price']) ?></td>
                        <td>
                            <input type="number" name="qty[<?= (int) $p['id'] ?>]" class="form-control form-control-sm"
                                   value="<?= (int) $line['quantity'] ?>" min="0" max="<?= (int) $p['stock'] ?>">
                        </td>
                        <td class="text-end"><?= format_money($line['line_total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="text-end fs-5 d-flex align-items-center justify-content-end gap-2">
            <?= icon('payments', 'icon-sm') ?>
            <span>Total: <strong><?= format_money($subtotal) ?></strong></span>
        </p>
        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <button type="submit" name="update_cart" value="1" class="btn btn-outline-secondary btn-icon-left">
                <?= icon('sync', 'icon-sm') ?>
                Update cart
            </button>
            <a href="checkout.php" class="btn btn-primary btn-icon-left">
                <?= icon('shopping_bag', 'icon-sm') ?>
                Proceed to checkout
            </a>
        </div>
    </form>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
