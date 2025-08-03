<?php $title = 'Add New Product'; ?>

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">New Product Details</h5>

            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/admin/products/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="form-group mb-3">
                    <label for="name">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="price">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                </div>
                <div class="form-group mb-3">
                    <label for="image">Image URL</label>
                    <input type="text" class="form-control" id="image" name="image" placeholder="/assets/images/new-product.jpg" required>
                </div>
                <div class="form-group mb-3">
                    <label for="file_url">File URL (Download Path)</label>
                    <input type="text" class="form-control" id="file_url" name="file_url" placeholder="/downloads/product.zip" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="featured" name="featured">
                    <label class="form-check-label" for="featured">
                        Feature this product on the homepage
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="/admin/products" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
