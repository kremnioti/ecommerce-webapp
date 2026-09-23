<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Product Catalog';

// Quick add from catalog cards (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_add'])) {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $qty = max(1, (int) ($_POST['quantity'] ?? 1));
    $result = cart_add_product($pdo, $productId, $qty);
    flash_set($result['ok'] ? 'success' : 'danger', $result['message']);
    header('Location: index.php');
    exit;
}

$flash = flash_consume();

// Load all products for the grid (stock needed for qty max)
$stmt = $pdo->query('SELECT id, name, slug, description, price, image_url, stock FROM products ORDER BY name');
$products = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<h1 class="h2 mb-2">Product catalog</h1>
<p class="text-muted mb-4">Set a quantity and tap <strong>+</strong> on a card to add to your cart, or open details for more info.</p>

<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <?php foreach ($products as $product): ?>
        <?php $maxQty = max(1, (int) $product['stock']); ?>
        <div class="col-sm-6 col-lg-4">
            <article class="card h-100 product-card shadow-sm">
                <img src="<?= e($product['image_url']) ?>" class="card-img-top product-thumb" alt="<?= e($product['name']) ?>">
                <div class="card-body d-flex flex-column">
                    <h2 class="h5 card-title"><?= e($product['name']) ?></h2>
                    <p class="card-text text-muted small flex-grow-1">
                        <?= e(mb_strimwidth($product['description'], 0, 90, '…')) ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary"><?= format_money((float) $product['price']) ?></span>
                        <a href="product.php?slug=<?= e(urlencode($product['slug'])) ?>" class="btn btn-outline-primary btn-sm">
                            Details
                        </a>
                    </div>

                    <?php if ((int) $product['stock'] < 1): ?>
                        <p class="text-danger small mt-2 mb-0">Out of stock</p>
                    <?php else: ?>
                        <form method="post" class="quick-add-form mt-3" data-max-qty="<?= $maxQty ?>">
                            <input type="hidden" name="quick_add" value="1">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <label class="form-label small text-muted mb-1">Qty</label>
                            <div class="d-flex align-items-stretch gap-2">
                                <div class="input-group input-group-sm qty-stepper">
                                    <button type="button" class="btn btn-outline-secondary qty-step" data-step="-1" aria-label="Decrease quantity">−</button>
                                    <input type="number" name="quantity" class="form-control text-center qty-input"
                                           value="1" min="1" max="<?= $maxQty ?>" aria-label="Quantity for <?= e($product['name']) ?>">
                                    <button type="button" class="btn btn-outline-secondary qty-step" data-step="1" aria-label="Increase quantity">+</button>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm quick-add-submit px-3" aria-label="Add <?= e($product['name']) ?> to cart" title="Add to cart">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>

<?php if (count($products) === 0): ?>
    <div class="alert alert-warning">No products yet. Import <code>database.sql</code> to add sample data.</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
