<?php $title = 'Manage Products'; ?>

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Products</h1>
        <a href="/admin/products/create" class="btn btn-primary">Add New Product</a>
    </div>

    <?php if (isset($_GET['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_GET['success_message']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['created_at']; ?></td>
                                <td>
                                    <a href="/admin/products/edit?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    <form action="/admin/products/delete" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
