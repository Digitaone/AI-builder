<?php $title = $product['name'] ?? 'Product Details'; ?>

<?php include 'partials/header.php'; ?>

<section class="container">
    <?php if (isset($product) && !empty($product)): ?>
        <div class="product-detail-container">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
                <div class="description">
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                <form action="/cart/add" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="btn">Add to Cart</button>
                </form>
            </div>
        </div>

        <hr style="margin: 3rem 0;">

        <!-- Reviews Section -->
        <div class="reviews-section">
            <h2>Customer Reviews (<?php echo count($reviews); ?>)</h2>
            <div class="reviews-summary">
                <strong>Average Rating:</strong> <?php echo number_format($avg_rating, 1); ?> / 5.0
            </div>

            <!-- Review Submission Form -->
            <?php if ($can_review): ?>
                <div class="review-form">
                    <h3>Leave a Review</h3>
                    <form action="/reviews/create" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="form-group">
                            <label for="rating">Your Rating</label>
                            <select name="rating" id="rating" class="form-control" required>
                                <option value="">Select a rating</option>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Good</option>
                                <option value="3">3 - Average</option>
                                <option value="2">2 - Fair</option>
                                <option value="1">1 - Poor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="comment">Your Review</label>
                            <textarea name="comment" id="comment" rows="4" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn">Submit Review</button>
                    </form>
                </div>
            <?php elseif(isset($_SESSION['user_id'])): ?>
                <p>You must purchase this product to leave a review.</p>
            <?php else: ?>
                <p><a href="/login?redirect=/product?id=<?php echo $product['id']; ?>">Log in</a> to leave a review.</p>
            <?php endif; ?>

            <!-- Existing Reviews -->
            <div class="reviews-list">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-meta">
                                <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                <span>- <?php echo date('F j, Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <div class="review-rating">
                                Rating: <?php echo $review['rating']; ?> / 5
                            </div>
                            <div class="review-comment">
                                <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No reviews yet. Be the first to leave one!</p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <h1>Product not found</h1>
        <p>Sorry, the product you are looking for does not exist.</p>
        <a href="/products">Back to Products</a>
    <?php endif; ?>
</section>

<?php include 'partials/footer.php'; ?>
