<?php
$pageTitle = $pageTitle ?? 'Shop';
$cartCount = cart_count_items();
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php', '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Simple Shop MVP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="site-body">
<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg navbar-modern">
        <div class="container">
            <a class="navbar-brand site-brand" href="index.php">
                <?= icon('storefront', 'brand-mark') ?>
                <span>Simple Shop</span>
            </a>
            <button class="navbar-toggler navbar-toggler-modern" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <?= icon('menu') ?>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern<?= $currentPage === 'index' ? ' active' : '' ?>" href="index.php">
                            <?= icon('grid_view', 'nav-icon') ?>
                            <span>Catalog</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern nav-link-cart<?= $currentPage === 'cart' ? ' active' : '' ?>" href="cart.php">
                            <?= icon('shopping_cart', 'nav-icon') ?>
                            <span>Cart</span>
                            <?php if ($cartCount > 0): ?>
                                <span class="cart-badge"><?= (int) $cartCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main class="container site-main pt-2 pb-5">
