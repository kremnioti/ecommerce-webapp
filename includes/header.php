<?php
$pageTitle = $pageTitle ?? 'Shop';
$cartCount = cart_count_items();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Simple Shop MVP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="index.php">Simple Shop</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Catalog</a>
            <a class="nav-link" href="cart.php">
                Cart
                <?php if ($cartCount > 0): ?>
                    <span class="badge bg-light text-primary cart-badge"><?= (int) $cartCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</nav>
<main class="container pb-5">
