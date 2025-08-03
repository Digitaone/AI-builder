<?php $title = 'Login'; ?>

<?php include 'partials/header.php'; ?>

<div class="form-container">
    <h2>Login to Your Account</h2>

    <?php if (isset($_GET['registered']) && $_GET['registered'] === 'success'): ?>
        <div class="alert alert-success">
            Registration successful! You can now log in.
        </div>
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">
        Don't have an account? <a href="/register">Register here</a>.
    </p>
</div>

<?php include 'partials/footer.php'; ?>
