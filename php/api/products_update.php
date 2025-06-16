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

// Method check (Using POST for multipart/form-data compatibility)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed for updates with potential file uploads.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// Define upload directories (relative to this script)
$base_upload_dir = '../../uploads/';
$product_files_dir = $base_upload_dir . 'products/';
$cover_images_dir = $base_upload_dir . 'covers/';

// Ensure directories exist (might be redundant if create script already ran, but good for robustness)
if (!is_dir($product_files_dir)) mkdir($product_files_dir, 0755, true);
if (!is_dir($cover_images_dir)) mkdir($cover_images_dir, 0755, true);

// Sanitize input function
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// File upload handling function (same as in products_create.php)
function handle_upload($file_key, $upload_dir, $allowed_types, $max_size_mb = 50) {
    // No file uploaded or error UPLOAD_ERR_NO_FILE is not an error for updates, just means no change.
    if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] == UPLOAD_ERR_NO_FILE) {
        return ['filepath' => null]; // No new file
    }
    if ($_FILES[$file_key]['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload error for ' . $file_key . ': Code ' . $_FILES[$file_key]['error']];
    }

    $file = $_FILES[$file_key];
    $file_size = $file['size'];
    $file_tmp_name = $file['tmp_name'];
    $file_name = $file['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if ($file_size > $max_size_mb * 1024 * 1024) {
        return ['error' => $file_key . ' exceeds maximum size of ' . $max_size_mb . 'MB.'];
    }
    if (!in_array($file_ext, $allowed_types)) {
        return ['error' => $file_key . ' has an invalid file type. Allowed: ' . implode(', ', $allowed_types)];
    }
    $new_file_name = uniqid('', true) . '-' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($file_name));
    $destination = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_tmp_name, $destination)) {
        return ['filepath' => str_replace('../../', '', $destination)]; // Relative path from web root 'uploads'
    } else {
        return ['error' => 'Failed to move uploaded file ' . $file_key . '.'];
    }
}

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
if (!$product_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required and must be an integer.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// Fetch existing product to get old file paths and check existence
$stmt_old = $conn->prepare("SELECT file_path, cover_image_path FROM products WHERE product_id = ?");
$stmt_old->bind_param("i", $product_id);
$stmt_old->execute();
$old_product_data = $stmt_old->get_result()->fetch_assoc();
$stmt_old->close();

if (!$old_product_data) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}
$old_file_path = $old_product_data['file_path'];
$old_cover_image_path = $old_product_data['cover_image_path'];

// Retrieve and sanitize other POST data
$product_name = isset($_POST['product_name']) ? sanitize_input($_POST['product_name']) : null;
$description = isset($_POST['description']) ? sanitize_input($_POST['description']) : null;
$price = isset($_POST['price']) ? filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT) : null;
$category_id = isset($_POST['category_id']) ? filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) : null;
$stock_available = (isset($_POST['stock_available']) && $_POST['stock_available'] !== '') ? filter_input(INPUT_POST, 'stock_available', FILTER_VALIDATE_INT) : (isset($_POST['stock_available']) && $_POST['stock_available'] === '' ? null : 'OMIT'); // OMIT if not set


// --- Input Validation ---
$errors = [];
if ($product_name === '') $errors[] = "Product name cannot be empty if provided."; // Allow not providing, but not empty string
if ($description === '') $errors[] = "Description cannot be empty if provided.";
if ($price !== null && ($price === false || $price < 0)) $errors[] = "If provided, price must be a valid positive number.";
if ($category_id !== null && $category_id === false) $errors[] = "If provided, category ID must be a valid integer.";
if ($stock_available !== 'OMIT' && $stock_available === false && $stock_available !== null) $errors[] = "If provided, stock available must be a valid integer or empty for unlimited.";


// Validate category_id exists if provided
if ($category_id !== null && $category_id !== false) {
    $stmt_cat = $conn->prepare("SELECT category_id FROM categories WHERE category_id = ?");
    $stmt_cat->bind_param("i", $category_id);
    $stmt_cat->execute();
    $stmt_cat->store_result();
    if ($stmt_cat->num_rows === 0) {
        $errors[] = "Selected category does not exist.";
    }
    $stmt_cat->close();
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Input validation failed.', 'errors' => $errors]);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// --- File Handling for new uploads ---
$new_product_file_path = null;
$new_cover_image_path = null;

if (isset($_FILES['product_file']) && $_FILES['product_file']['error'] != UPLOAD_ERR_NO_FILE) {
    $product_file_result = handle_upload('product_file', $product_files_dir, ['zip', 'pdf', 'epub', 'jpg', 'png'], 1024);
    if (isset($product_file_result['error'])) $errors[] = $product_file_result['error'];
    else $new_product_file_path = $product_file_result['filepath'];
}
if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] != UPLOAD_ERR_NO_FILE) {
    $cover_image_result = handle_upload('cover_image', $cover_images_dir, ['jpg', 'jpeg', 'png', 'gif'], 10);
    if (isset($cover_image_result['error'])) $errors[] = $cover_image_result['error'];
    else $new_cover_image_path = $cover_image_result['filepath'];
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'File upload failed.', 'errors' => $errors]);
    // Cleanup newly uploaded files if one succeeded and other failed or validation failed later
    if ($new_product_file_path && file_exists($base_upload_dir . $new_product_file_path)) unlink($base_upload_dir . $new_product_file_path);
    if ($new_cover_image_path && file_exists($base_upload_dir . $new_cover_image_path)) unlink($base_upload_dir . $new_cover_image_path);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// --- Database Update ---
$sql_parts = [];
$params = [];
$types = "";

if ($product_name !== null) { $sql_parts[] = "product_name = ?"; $params[] = $product_name; $types .= "s"; }
if ($description !== null) { $sql_parts[] = "description = ?"; $params[] = $description; $types .= "s"; }
if ($price !== null) { $sql_parts[] = "price = ?"; $params[] = $price; $types .= "d"; }
if ($category_id !== null) { $sql_parts[] = "category_id = ?"; $params[] = $category_id; $types .= "i"; }
if ($new_product_file_path !== null) { $sql_parts[] = "file_path = ?"; $params[] = $new_product_file_path; $types .= "s"; }
if ($new_cover_image_path !== null) { $sql_parts[] = "cover_image_path = ?"; $params[] = $new_cover_image_path; $types .= "s"; }
if ($stock_available !== 'OMIT') { $sql_parts[] = "stock_available = ?"; $params[] = $stock_available; $types .= "i"; } // stock_available can be null

if (empty($sql_parts)) {
    echo json_encode(['status' => 'info', 'message' => 'No changes provided for update.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

$sql = "UPDATE products SET " . implode(", ", $sql_parts) . " WHERE product_id = ?";
$params[] = $product_id;
$types .= "i";

try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Update statement prepare failed: " . $conn->error . " SQL: " . $sql);

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Delete old files if new ones were uploaded and DB update was successful
            if ($new_product_file_path && $old_file_path && file_exists($base_upload_dir . $old_file_path)) {
                unlink($base_upload_dir . $old_file_path);
            }
            if ($new_cover_image_path && $old_cover_image_path && file_exists($base_upload_dir . $old_cover_image_path)) {
                unlink($base_upload_dir . $old_cover_image_path);
            }
            echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);
        } else {
            // No rows affected - could be same data or product_id not found (already checked, but still)
            echo json_encode(['status' => 'info', 'message' => 'No changes applied to the product. Data might be the same.']);
        }
    } else {
        throw new Exception("Failed to update product: " . $stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    error_log("Product update error: " . $e->getMessage());
    // Cleanup newly uploaded files if DB update failed
    if ($new_product_file_path && file_exists($base_upload_dir . $new_product_file_path)) unlink($base_upload_dir . $new_product_file_path);
    if ($new_cover_image_path && file_exists($base_upload_dir . $new_cover_image_path)) unlink($base_upload_dir . $new_cover_image_path);
    echo json_encode(['status' => 'error', 'message' => 'An internal server error occurred while updating the product.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
}
?>
