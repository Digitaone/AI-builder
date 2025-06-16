<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once '../includes/db_connect.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'User not logged in. Please login to continue.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

$user_id = $_SESSION['user_id'];
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';

// --- Basic Input Validation ---
$errors = [];
// Allow empty strings if user wants to remove their name, but check for data types if needed
if (!is_string($first_name)) {
    $errors[] = "First name must be a string.";
}
if (!is_string($last_name)) {
    $errors[] = "Last name must be a string.";
}

// Example length validation (optional)
if (strlen($first_name) > 100) {
    $errors[] = "First name cannot exceed 100 characters.";
}
if (strlen($last_name) > 100) {
    $errors[] = "Last name cannot exceed 100 characters.";
}

// For this specific profile update, we are updating first_name and last_name.
// It's okay if they are empty, as the user might want to clear these fields.
// If specific fields were mandatory, you would add:
// if (empty($first_name) && empty($last_name)) {
//     $errors[] = 'At least one field (first name or last name) must be provided for an update.';
// }

if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Input validation failed.', 'errors' => $errors]);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// --- Database Interaction ---
// $conn should be available from db_connect.php
try {
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Update profile statement prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssi", $first_name, $last_name, $user_id);

    if ($stmt->execute()) {
        // Optionally, update session variables if these names are stored and used directly from session
        // For example, if you display "Welcome, $_SESSION['first_name']"
        $_SESSION['first_name'] = $first_name; // Update session if you store/use it
        $_SESSION['last_name'] = $last_name;   // Update session if you store/use it

        http_response_code(200); // OK
        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'data' => [
                'first_name' => $first_name,
                'last_name' => $last_name
            ]
        ]);
    } else {
        throw new Exception("Profile update execution failed: " . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("User profile update error for user_id {$user_id}: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile due to a server error.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
