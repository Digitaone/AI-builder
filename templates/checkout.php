<?php $title = 'Checkout'; ?>

<?php include 'partials/header.php'; ?>

<section class="container">
    <h1>Checkout</h1>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            There was an error with your payment. Please try again.
        </div>
    <?php endif; ?>

    <div class="checkout-container">
        <div class="customer-info">
            <h2>Payment Information</h2>
            <p>Please review your order summary and click "Place Order" to complete your purchase.</p>
            <form action="/checkout" method="POST" id="checkout-form">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <!-- In a real application, this would contain payment fields from Stripe/PayPal -->
                <div class="form-group">
                    <label for="email">Email for Receipt</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required disabled>
                    <small>Your digital products will be linked to this account.</small>
                </div>

                <!-- Placeholder for payment gateway element -->
                <div id="payment-element" style="border: 1px solid #ddd; padding: 1rem; border-radius: 5px; min-height: 100px;">
                    <p>This is where the secure payment form (e.g., Stripe or PayPal) would be mounted.</p>
                </div>

                <button type="submit" class="btn" style="margin-top: 2rem;">Place Order</button>
            </form>
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php if (!empty($cart_items)): ?>
                <?php foreach($cart_items as $item): ?>
                    <div class="summary-item">
                        <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                        <span>$<?php echo number_format($item['subtotal'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="summary-item summary-total">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>
