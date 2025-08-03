<?php
require_once 'auth.php'; // Auth check and DB connection

// Get post ID from URL and validate it
$id = $_GET['id'] ?? 0;
if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
    // Invalid ID, redirect or show an error
    header('Location: index.php?error=invalid_id');
    exit;
}

// Prepare and execute the DELETE statement
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Successfully deleted
    header("Location: index.php?message=deleted");
} else {
    // Deletion failed
    header("Location: index.php?error=delete_failed");
}

$stmt->close();
$conn->close();
exit;
?>
