<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once '../includes/db_connect.php'; // Database connection

// Admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Administrator privileges required.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// Method check (Allow POST for form-based delete, or DELETE)
// If using POST, a common practice is to send `_method=DELETE` or specific action.
// For this, we'll assume product_id is sent in request body (JSON) or as query param for DELETE.
$method = $_SERVER['REQUEST_METHOD'];
$product_id = null;

if ($method === 'DELETE') {
    // For DELETE, data might be in query string or JSON body
    $product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);
    if (!$product_id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $product_id = filter_var($data['product_id'] ?? null, FILTER_VALIDATE_INT);
    }
} elseif ($method === 'POST') { // Typically for form submission
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
     if (!$product_id) { // Fallback to JSON body for POST as well
        $data = json_decode(file_get_contents('php://input'), true);
        $product_id = filter_var($data['product_id'] ?? null, FILTER_VALIDATE_INT);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST or DELETE is allowed.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}


if (!$product_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required and must be an integer.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// Define base upload directory (relative to this script)
$base_upload_dir = '../../uploads/'; // Path from php/api/ to uploads/

try {
    // Fetch product data to get file paths before deleting the record
    $stmt_fetch = $conn->prepare("SELECT file_path, cover_image_path FROM products WHERE product_id = ?");
    if (!$stmt_fetch) throw new Exception("Prepare statement failed (fetch paths): " . $conn->error);
    $stmt_fetch->bind_param("i", $product_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    $product_data = $result->fetch_assoc();
    $stmt_fetch->close();

    if (!$product_data) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
        exit;
    }

    $file_to_delete_path = $product_data['file_path'];
    $cover_image_to_delete_path = $product_data['cover_image_path'];

    // Begin transaction for atomic operation (DB delete + file unlink)
    $conn->begin_transaction();

    // Delete the product record from the database
    $stmt_delete = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    if (!$stmt_delete) throw new Exception("Prepare statement failed (delete product): " . $conn->error);
    $stmt_delete->bind_param("i", $product_id);

    if ($stmt_delete->execute()) {
        if ($stmt_delete->affected_rows > 0) {
            $db_deleted = true;
        } else {
            // Product might have been deleted by another request between fetch and delete
            $conn->rollback(); // Rollback if product was not found for deletion
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Product not found during delete operation, or already deleted.']);
            $stmt_delete->close();
            if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
            exit;
        }
        $stmt_delete->close();

        $file_deletion_errors = [];
        // Delete associated files from server
        if ($file_to_delete_path) {
            $full_file_path = $base_upload_dir . $file_to_delete_path;
            if (file_exists($full_file_path)) {
                if (!unlink($full_file_path)) {
                    $file_deletion_errors[] = "Failed to delete product file: " . $file_to_delete_path;
                }
            } else {
                // $file_deletion_errors[] = "Product file not found on server: " . $file_to_delete_path; // Optional: log this
            }
        }
        if ($cover_image_to_delete_path) {
            $full_cover_path = $base_upload_dir . $cover_image_to_delete_path;
            if (file_exists($full_cover_path)) {
                if (!unlink($full_cover_path)) {
                    $file_deletion_errors[] = "Failed to delete cover image: " . $cover_image_to_delete_path;
                }
            } else {
                // $file_deletion_errors[] = "Cover image not found on server: " . $cover_image_to_delete_path; // Optional: log this
            }
        }

        if (empty($file_deletion_errors)) {
            $conn->commit(); // Commit transaction if DB delete and file unlinks were successful
            echo json_encode(['status' => 'success', 'message' => 'Product and associated files deleted successfully.']);
        } else {
            // Files failed to delete. CRITICAL: Rollback DB change.
            // This indicates a potential issue with file permissions or paths.
            // For a robust system, you might flag for manual cleanup instead of rollback,
            // or retry file deletion. For simplicity here, we rollback.
            $conn->rollback();
            http_response_code(500);
            error_log("Product (ID: {$product_id}) DB record deleted, but file unlink failed: " . implode(", ", $file_deletion_errors));
            echo json_encode(['status' => 'error', 'message' => 'Product record was deleted, but failed to delete associated files. Operation rolled back. Please check server logs.', 'errors' => $file_deletion_errors]);
        }

    } else {
        $conn->rollback(); // Rollback if DB deletion itself failed
        throw new Exception("Failed to delete product from database: " . $stmt_delete->error);
    }

} catch (Exception $e) {
    if ($conn->autocommit === FALSE) { // Check if in transaction before trying to rollback
       if ($conn->server_version >= 50000) { // mysqli_ping available
            if ($conn->ping()) $conn->rollback();
       } else {
            // Cannot reliably check transaction status or ping, attempt rollback cautiously
            // @$conn->rollback(); // Suppress error if not in transaction
       }
    }
    http_response_code(500);
    error_log("Product delete error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An internal server error occurred while deleting the product.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        // Ensure autocommit is restored if it was changed, though begin_transaction handles this for InnoDB.
        // $conn->autocommit(TRUE);
        $conn->close();
    }
}
?>
