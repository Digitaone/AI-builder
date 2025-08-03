<?php $title = 'Our Products'; ?>

<?php include 'partials/header.php'; ?>

<section class="container">
    <h2>All Products</h2>
    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="product-card-content">
                        <h3><a href="/product/<?php echo $product['id']; ?>/<?php echo slugify($product['name']); ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                        <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
                        <a href="/product/<?php echo $product['id']; ?>/<?php echo slugify($product['name']); ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'partials/footer.php'; ?>
