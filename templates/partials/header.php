<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Digital Products Store'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description ?? 'The best place to buy digital products like ebooks, software, and templates.'); ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="/">DigitalStore</a>
            </div>
            <nav>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/products">Products</a></li>
                    <?php
                        $cart_item_count = 0;
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            $cart_item_count = array_sum($_SESSION['cart']);
                        }
                    ?>
                    <li><a href="/cart">Cart (<?php echo $cart_item_count; ?>)</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/dashboard">Dashboard</a></li>
                        <li><a href="/logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="/login">Login</a></li>
                        <li><a href="/register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
