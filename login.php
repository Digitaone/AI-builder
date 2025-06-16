<?php include 'templates/header.php'; ?>
<main>
    <h2>Login</h2>
    <form id="loginForm">
        <div>
            <label for="login_email">Email:</label>
            <input type="email" id="login_email" name="email" required>
        </div>
        <div>
            <label for="login_password">Password:</label>
            <input type="password" id="login_password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <div id="loginMessages"></div>
</main>
<?php include 'templates/footer.php'; ?>
