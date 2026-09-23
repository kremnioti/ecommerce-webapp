<?php
/**
 * Shared bootstrap: session, DB, cart helpers.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

/** Cart is stored in session as [ product_id => quantity ] */
function cart_get(): array
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

function cart_count_items(): int
{
    $total = 0;
    foreach (cart_get() as $qty) {
        $total += (int) $qty;
    }
    return $total;
}

function cart_set_quantity(int $productId, int $quantity): void
{
    $cart = cart_get();
    if ($quantity <= 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = $quantity;
    }
    $_SESSION['cart'] = $cart;
}

/**
 * Add more of a product to the cart (respects stock).
 * Returns ['ok' => bool, 'message' => string].
 */
function cart_add_product(PDO $pdo, int $productId, int $quantityToAdd): array
{
    if ($quantityToAdd < 1) {
        return ['ok' => false, 'message' => 'Quantity must be at least 1.'];
    }

    $stmt = $pdo->prepare('SELECT id, name, stock FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        return ['ok' => false, 'message' => 'Product not found.'];
    }

    $stock = (int) $product['stock'];
    $cart = cart_get();
    $current = (int) ($cart[$productId] ?? 0);
    $newTotal = $current + $quantityToAdd;

    if ($newTotal > $stock) {
        return ['ok' => false, 'message' => 'Not enough stock for ' . $product['name'] . '.'];
    }

    cart_set_quantity($productId, $newTotal);

    return [
        'ok' => true,
        'message' => $quantityToAdd . ' × ' . $product['name'] . ' added to cart.',
    ];
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** @return array{type: string, message: string}|null */
function flash_consume(): ?array
{
    if (empty($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function format_money(float $amount): string
{
    return '$' . number_format($amount, 2);
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
