<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once '../includes/db_connect.php'; // Database connection
// require_once '../includes/functions.php'; // For any helper functions

// Check if user is admin and logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'Access denied. You must be an administrator to create products.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// Only allow POST request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// Define upload directories
// IMPORTANT: These paths are relative to this script's location (php/api/)
$base_upload_dir = '../../uploads/'; // Moves up two levels to the root 'uploads' directory
$product_files_dir = $base_upload_dir . 'products/';
$cover_images_dir = $base_upload_dir . 'covers/';

// Create directories if they don't exist
if (!is_dir($product_files_dir) && !mkdir($product_files_dir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to create product files directory. Check permissions. Path: ' . realpath($product_files_dir)]);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}
if (!is_dir($cover_images_dir) && !mkdir($cover_images_dir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to create cover images directory. Check permissions. Path: ' . realpath($cover_images_dir)]);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}


// Sanitize input function (basic example)
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(stripslashes(trim($data)));
}

// File upload handling function
function handle_upload($file_key, $upload_dir, $allowed_types, $max_size_mb = 50) {
    if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['error' => ucfirst(str_replace('_', ' ', $file_key)) . ' is required.'];
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

    // Generate unique filename
    $new_file_name = uniqid('', true) . '-' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($file_name));
    $destination = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_tmp_name, $destination)) {
        // Return relative path from the web root 'uploads' directory for DB storage
        return ['filepath' => str_replace('../../', '', $destination)];
    } else {
        return ['error' => 'Failed to move uploaded file ' . $file_key . '. Check server permissions.'];
    }
}


// Retrieve and sanitize POST data
$product_name = sanitize_input($_POST['product_name'] ?? '');
$description = sanitize_input($_POST['description'] ?? '');
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
$stock_available = isset($_POST['stock_available']) && $_POST['stock_available'] !== '' ? filter_input(INPUT_POST, 'stock_available', FILTER_VALIDATE_INT) : null;


// --- Input Validation ---
$errors = [];
if (empty($product_name)) $errors[] = "Product name is required.";
if (empty($description)) $errors[] = "Description is required.";
if ($price === false || $price < 0) $errors[] = "Valid price is required (must be a positive number).";
if ($category_id === false) $errors[] = "Valid category ID is required.";
if ($stock_available !== null && $stock_available === false) $errors[] = "Stock available must be a valid integer or empty for unlimited.";


// Validate category_id exists
if ($category_id !== false) {
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
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Input validation failed.', 'errors' => $errors]);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

// --- File Handling ---
// Define allowed file types (examples, adjust as needed)
$allowed_product_file_types = ['zip', 'pdf', 'epub', 'mobi', 'jpg', 'png', 'mp3', 'mp4']; // For digital products
$allowed_cover_image_types = ['jpg', 'jpeg', 'png', 'gif'];

$product_file_result = handle_upload('product_file', $product_files_dir, $allowed_product_file_types, 1024); // Max 1GB for product file
$cover_image_result = handle_upload('cover_image', $cover_images_dir, $allowed_cover_image_types, 10); // Max 10MB for cover

if (isset($product_file_result['error'])) $errors[] = $product_file_result['error'];
if (isset($cover_image_result['error'])) $errors[] = $cover_image_result['error'];

if (!empty($errors)) {
    http_response_code(400); // Bad Request for file issues
    echo json_encode(['status' => 'error', 'message' => 'File upload failed.', 'errors' => $errors]);
    // Cleanup already uploaded file if one succeeded and the other failed (optional advanced step)
    if (isset($product_file_result['filepath']) && file_exists($base_upload_dir . $product_file_result['filepath'])) unlink($base_upload_dir . $product_file_result['filepath']);
    if (isset($cover_image_result['filepath']) && file_exists($base_upload_dir . $cover_image_result['filepath'])) unlink($base_upload_dir . $cover_image_result['filepath']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

$product_file_path = $product_file_result['filepath'];
$cover_image_path = $cover_image_result['filepath'];

// --- Database Interaction ---
try {
    $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, category_id, file_path, cover_image_path, stock_available) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Product insert statement prepare failed: " . $conn->error);
    }
    // Note: stock_available can be NULL if not provided or empty string.
    // The binding type 'd' for decimal, 'i' for integer, 's' for string.
    // If stock_available is null, it should be bound as null. The schema handles default.
    $stmt->bind_param("ssdiisi", $product_name, $description, $price, $category_id, $product_file_path, $cover_image_path, $stock_available);

    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;
        http_response_code(201); // Created
        echo json_encode([
            'status' => 'success',
            'message' => 'Product created successfully.',
            'product_id' => $product_id,
            'file_path' => $product_file_path,
            'cover_image_path' => $cover_image_path
        ]);
    } else {
        throw new Exception("Failed to create product: " . $stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("Product creation error: " . $e->getMessage());
    // Cleanup uploaded files if DB insert fails
    if (isset($product_file_path) && file_exists($base_upload_dir . $product_file_path)) unlink($base_upload_dir . $product_file_path);
    if (isset($cover_image_path) && file_exists($base_upload_dir . $cover_image_path)) unlink($base_upload_dir . $cover_image_path);
    echo json_encode(['status' => 'error', 'message' => 'An internal server error occurred while creating the product.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
