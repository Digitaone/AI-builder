<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// This script should ideally only be accessible via GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'loggedIn' => false, 'message' => 'Invalid request method. Only GET is allowed.']);
    exit;
}

if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // User is logged in, return session data
    // Ensure all sensitive data is necessary for the client-side
    // Avoid sending password hashes or overly sensitive info
    $userData = [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];

    // Add first_name and last_name if they exist in session (updated by profile update)
    if (isset($_SESSION['first_name'])) {
        $userData['first_name'] = $_SESSION['first_name'];
    }
    if (isset($_SESSION['last_name'])) {
        $userData['last_name'] = $_SESSION['last_name'];
    }

    echo json_encode([
        'status' => 'success',
        'loggedIn' => true,
        'user' => $userData
    ]);
} else {
    // User is not logged in
    echo json_encode([
        'status' => 'success', // The request itself was successful, but user is not authenticated
        'loggedIn' => false
    ]);
}
exit;
?>
