<?php $title = 'Register'; ?>

<?php include 'partials/header.php'; ?>

<div class="form-container">
    <h2>Create an Account</h2>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/register" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn">Register</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">
        Already have an account? <a href="/login">Login here</a>.
    </p>
</div>

<?php include 'partials/footer.php'; ?>
