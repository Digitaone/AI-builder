<?php include 'templates/header.php'; ?>
<main>
    <h2>Register</h2>
    <form id="registrationForm">
        <div>
            <label for="reg_username">Username:</label>
            <input type="text" id="reg_username" name="username" required>
        </div>
        <div>
            <label for="reg_email">Email:</label>
            <input type="email" id="reg_email" name="email" required>
        </div>
        <div>
            <label for="reg_password">Password:</label>
            <input type="password" id="reg_password" name="password" required>
        </div>
        <div>
            <label for="reg_confirm_password">Confirm Password:</label>
            <input type="password" id="reg_confirm_password" name="confirm_password" required>
        </div>
        <div>
            <label for="reg_first_name">First Name (Optional):</label>
            <input type="text" id="reg_first_name" name="first_name">
        </div>
        <div>
            <label for="reg_last_name">Last Name (Optional):</label>
            <input type="text" id="reg_last_name" name="last_name">
        </div>
        <button type="submit">Register</button>
    </form>
    <div id="registrationMessages"></div>
</main>
<?php include 'templates/footer.php'; ?>
