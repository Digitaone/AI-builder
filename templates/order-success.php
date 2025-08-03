<?php $title = 'Order Successful'; ?>

<?php include 'partials/header.php'; ?>

<section class="container">
    <div class="success-container">
        <h1>Thank You For Your Order!</h1>
        <?php if (isset($order_id) && $order_id): ?>
            <p>Your order has been placed successfully. Your order number is <strong>#<?php echo htmlspecialchars($order_id); ?></strong>.</p>
        <?php else: ?>
            <p>Your order has been placed successfully.</p>
        <?php endif; ?>
        <p>Your digital products are now available in your account dashboard.</p>
        <div style="margin-top: 2rem;">
            <a href="/dashboard" class="btn">Go to Dashboard</a>
            <a href="/products" class="btn" style="background-color: #f0ad4e; margin-top: 1rem;">Continue Shopping</a>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>
