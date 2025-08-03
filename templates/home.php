<?php $title = 'Welcome to Digital Store'; ?>

<?php include 'partials/header.php'; ?>

<section class="hero">
    <div class="hero-content">
        <h1>Find Your Next Digital Product</h1>
        <p>Ebooks, software, templates, and more. Instantly delivered.</p>
        <a href="/products" class="btn">Browse Products</a>
    </div>
</section>

<section class="container featured-products">
    <h2>Featured Products</h2>
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
            <p>No featured products available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'partials/footer.php'; ?>
