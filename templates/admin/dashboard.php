<?php $title = 'Dashboard'; ?>

<?php include 'partials/header.php'; ?>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Users</h3>
        <p class="stat-number"><?php echo $stats['user_count']; ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Orders</h3>
        <p class="stat-number"><?php echo $stats['order_count']; ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Sales</h3>
        <p class="stat-number">$<?php echo number_format($stats['total_sales'], 2); ?></p>
    </div>
</div>

<div style="margin-top: 2rem; background: #fff; padding: 1.5rem; border-radius: 5px;">
    <h3>Welcome to the Admin Panel</h3>
    <p>From here you can manage the products, orders, and users on your site.</p>
    <p>Use the navigation on the left to get started.</p>
</div>

<?php include 'partials/footer.php'; ?>
