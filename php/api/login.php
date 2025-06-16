<?php
// Start session at the very beginning of the script
// This is crucial for session variables to work correctly.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once '../includes/db_connect.php'; // Database connection

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
    exit;
}

// Get POST data (assuming login with email, can be adapted for username)
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// --- Basic Input Validation ---
$errors = [];
if (empty($email)) {
    $errors[] = "Email is required.";
}
if (empty($password)) {
    $errors[] = "Password is required.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // This check is only relevant if logging in with email.
    // If allowing username login, this part would need adjustment or removal.
    $errors[] = "Invalid email format.";
}

if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Input validation failed.', 'errors' => $errors]);
    exit;
}

// --- Database Interaction & Authentication ---
// $conn should be available from db_connect.php

try {
    // Prepare SQL statement to fetch user by email
    // You could adapt this to also check for username if you want to allow login with either:
    // $stmt = $conn->prepare("SELECT user_id, username, email, password_hash, role FROM users WHERE email = ? OR username = ?");
    // $stmt->bind_param("ss", $identifier, $identifier); // $identifier would be $email or $username
    $stmt = $conn->prepare("SELECT user_id, username, email, password_hash, role FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Login statement prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result(); // Get result set for fetching data

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc(); // Fetch user data as an associative array

        // Verify the password
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, proceed to start session

            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Store essential user information in the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true; // A flag to easily check login status

            http_response_code(200); // OK
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful.',
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            // Password verification failed
            http_response_code(401); // Unauthorized
            echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']); // Generic error
        }
    } else {
        // User not found (or multiple users, which shouldn't happen with unique email)
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']); // Generic error
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    // Log the detailed error message to the server's error log
    error_log("User login error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Login failed due to a server error. Please try again later.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
