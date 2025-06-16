<?php
header('Content-Type: application/json');
require_once '../includes/db_connect.php'; // Adjust path as needed
// require_once '../includes/functions.php'; // If you create helper functions

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
    exit;
}

// Get POST data (ensure your frontend is sending data with these names)
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$first_name = $_POST['first_name'] ?? null; // Optional
$last_name = $_POST['last_name'] ?? null;   // Optional

// --- Basic Input Validation ---
$errors = [];

if (empty($username)) {
    $errors[] = "Username is required.";
}
if (empty($email)) {
    $errors[] = "Email is required.";
}
if (empty($password)) {
    $errors[] = "Password is required.";
}
if (empty($confirm_password)) {
    $errors[] = "Confirm password is required.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

// Basic password strength: minimum 8 characters
if (!empty($password) && strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
}

if (!empty($password) && $password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Input validation failed.', 'errors' => $errors]);
    exit;
}

// --- Database Interaction ---
// $conn should be available from db_connect.php

// Check if username already exists
try {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    if (!$stmt) {
        throw new Exception("Username check statement prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['status' => 'error', 'message' => 'Username already taken.']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Email check statement prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['status' => 'error', 'message' => 'Email already registered.']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Hash the password securely
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    if ($password_hash === false) {
        throw new Exception("Password hashing failed.");
    }

    // Prepare SQL INSERT statement
    // Default role 'customer' is set by database schema, so not explicitly included here
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Insert user statement prepare failed: " . $conn->error);
    }

    // Bind parameters: s = string. If first_name or last_name are null, they will be inserted as NULL.
    $stmt->bind_param("sssss", $username, $email, $password_hash, $first_name, $last_name);

    // Execute the statement
    if ($stmt->execute()) {
        $new_user_id = $stmt->insert_id; // Get the ID of the newly inserted user
        http_response_code(201); // Created
        echo json_encode([
            'status' => 'success',
            'message' => 'User registered successfully.',
            'user_id' => $new_user_id
        ]);
    } else {
        throw new Exception("User registration execution failed: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    // Log the detailed error message to the server's error log for administrators
    error_log("User registration error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Registration failed due to a server error. Please try again later.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
