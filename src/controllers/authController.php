<?php

/**
 * Displays the registration form.
 */
function show_register_form() {
    render_view('register');
}

/**
 * Handles the registration form submission.
 *
 * @param PDO $pdo The database connection object.
 */
function handle_registration(PDO $pdo) {
    verify_csrf_token();
    $errors = [];

    // --- Validation ---
    if (empty($_POST['username'])) {
        $errors[] = 'Username is required.';
    }
    if (empty($_POST['email'])) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is not valid.';
    }
    if (empty($_POST['password'])) {
        $errors[] = 'Password is required.';
    } elseif (strlen($_POST['password']) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if ($_POST['password'] !== $_POST['password_confirm']) {
        $errors[] = 'Passwords do not match.';
    }

    // --- Check if user already exists ---
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$_POST['username'], $_POST['email']]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already exists.';
        }
    }

    // --- If no errors, create user ---
    if (empty($errors)) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');

        try {
            $stmt->execute([$_POST['username'], $_POST['email'], $password_hash]);
            // Redirect to login page with a success message
            header('Location: /login?registered=success');
            exit;
        } catch (PDOException $e) {
            // In a real app, log this error
            $errors[] = 'An error occurred during registration. Please try again.';
        }
    }

    // --- If there are errors, show the form again with errors ---
    render_view('register', ['errors' => $errors]);
}

/**
 * Displays the login form.
 */
function show_login_form() {
    render_view('login');
}

/**
 * Handles the login form submission.
 *
 * @param PDO $pdo The database connection object.
 */
function handle_login(PDO $pdo) {
    verify_csrf_token();
    $errors = [];
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = (bool)$user['is_admin']; // Store admin status

            if ($_SESSION['is_admin']) {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /'); // Redirect non-admins to homepage
            }
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }

    render_view('login', ['errors' => $errors]);
}

/**
 * Logs the user out.
 */
function logout() {
    // Unset all of the session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();

    // Redirect to homepage
    header('Location: /');
    exit;
}
