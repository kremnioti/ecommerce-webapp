<?php
require_once __DIR__ . '/includes/bootstrap.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product not found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-danger alert-with-icon">' . icon('search_off', 'icon-sm') . '<span>Product not found. <a href="index.php">Back to catalog</a></span></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $product['name'];

// Handle add-to-cart form (POST)
$message = '';
$messageType = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = max(1, (int) ($_POST['quantity'] ?? 1));
    $result = cart_add_product($pdo, (int) $product['id'], $qty);
    $message = $result['message'];
    $messageType = $result['ok'] ? 'success' : 'danger';
}

require __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= e($messageType) ?> alert-with-icon">
        <?= icon($messageType === 'success' ? 'check_circle' : 'error', 'icon-sm') ?>
        <span><?= e($message) ?></span>
    </div>
<?php endif; ?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php"><?= icon('home', 'icon-sm') ?> Catalog</a></li>
        <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
    </ol>
</nav>

<div class="row g-4 align-items-start">
    <div class="col-md-5">
        <img src="<?= e($product['image_url']) ?>" class="img-fluid rounded shadow-sm product-detail-img" alt="<?= e($product['name']) ?>">
    </div>
    <div class="col-md-7">
        <h1 class="h2 d-flex align-items-center gap-2"><?= icon('inventory_2') ?><?= e($product['name']) ?></h1>
        <p class="display-6 text-primary fw-bold price-tag"><?= icon('sell') ?><?= format_money((float) $product['price']) ?></p>
        <p class="lead"><?= e($product['description']) ?></p>
        <p class="text-muted small d-flex align-items-center gap-1"><?= icon('inventory_2', 'icon-sm') ?>In stock: <?= (int) $product['stock'] ?></p>

        <form method="post" class="row g-2 align-items-end" style="max-width: 360px;">
            <div class="col-5">
                <label for="quantity" class="form-label form-label-with-icon"><?= icon('pin') ?>Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?= (int) $product['stock'] ?>">
            </div>
            <div class="col-7">
                <button type="submit" name="add_to_cart" value="1" class="btn btn-primary w-100 btn-icon-left">
                    <?= icon('add_shopping_cart', 'icon-sm') ?>
                    Add to cart
                </button>
            </div>
        </form>
        <a href="cart.php" class="btn btn-link mt-2 ps-0 btn-icon-left"><?= icon('shopping_cart', 'icon-sm') ?>Go to cart</a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
