<?php

/**
 * Checks if the current user is an administrator.
 * If not, it halts execution and shows a 403 Forbidden page.
 */
function require_admin() {
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        http_response_code(403);
        // It's good practice to have a specific view for this.
        // We can reuse the 404 view for now if 403 doesn't exist, or create a new one.
        render_view('403');
        exit;
    }
}

/**
 * Generates a CSRF token, stores it in the session, and returns it.
 *
 * @return string The generated CSRF token.
 */
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies the submitted CSRF token against the one in the session.
 * Halts execution if the token is invalid.
 */
function verify_csrf_token() {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Token is missing or invalid.
        unset($_SESSION['csrf_token']); // Invalidate the token
        http_response_code(403);
        die('CSRF token validation failed.');
    }
    // Invalidate the token after successful use to prevent reuse
    unset($_SESSION['csrf_token']);
}
