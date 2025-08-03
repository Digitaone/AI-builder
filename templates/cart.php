<?php $title = 'Your Shopping Cart'; ?>

<?php include 'partials/header.php'; ?>

<section class="container cart-container">
    <h1>Shopping Cart</h1>

    <?php if (!empty($cart_items)): ?>
        <div class="cart-items">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="cart-item-info">
                        <h3><a href="/product/<?php echo $item['id']; ?>/<?php echo slugify($item['name']); ?>"><?php echo htmlspecialchars($item['name']); ?></a></h3>
                        <p>Price: $<?php echo htmlspecialchars($item['price']); ?></p>
                    </div>
                    <div class="cart-item-actions">
                        <form action="/cart/update" method="POST" style="display: flex; align-items: center;">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <label for="quantity-<?php echo $item['id']; ?>">Qty:</label>
                            <input type="number" id="quantity-<?php echo $item['id']; ?>" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control">
                            <button type="submit" class="btn" style="width: auto; margin-left: 1rem;">Update</button>
                        </form>
                        <form action="/cart/remove" method="POST" style="margin-left: 1rem;">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn" style="background-color: #d9534f;">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <h3>Total: $<?php echo number_format($total, 2); ?></h3>
            <a href="/checkout" class="btn" style="margin-top: 1rem;">Proceed to Checkout</a>
        </div>

    <?php else: ?>
        <div class="empty-cart">
            <h2>Your cart is empty.</h2>
            <a href="/products" class="btn" style="margin-top: 1rem;">Continue Shopping</a>
        </div>
    <?php endif; ?>
</section>

<?php include 'partials/footer.php'; ?>
