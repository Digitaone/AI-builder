<?php $title = 'Manage Orders'; ?>

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-3">Orders</h1>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo htmlspecialchars($order['customer_email']); ?>)</td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                                <td><?php echo $order['created_at']; ?></td>
                                <td>
                                    <a href="/admin/orders/view?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
