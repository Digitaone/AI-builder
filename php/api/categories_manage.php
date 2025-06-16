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
    echo json_encode(['status' => 'error', 'message' => 'Access denied. You must be an administrator to manage categories.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// Sanitize input function (basic example)
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(stripslashes(trim($data)));
}

try {
    switch ($method) {
        case 'POST': // Create Category
            $data = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE && isset($_POST['category_name'])) {
                // Fallback for form-data if JSON decoding fails or for simpler forms
                $data = $_POST;
            }

            $category_name = sanitize_input($data['category_name'] ?? '');
            $category_description = sanitize_input($data['category_description'] ?? null);

            if (empty($category_name)) {
                http_response_code(400); // Bad Request
                echo json_encode(['status' => 'error', 'message' => 'Category name is required.']);
                break;
            }

            // Check if category name already exists
            $stmt_check = $conn->prepare("SELECT category_id FROM categories WHERE category_name = ?");
            $stmt_check->bind_param("s", $category_name);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                http_response_code(409); // Conflict
                echo json_encode(['status' => 'error', 'message' => 'Category name already exists.']);
                $stmt_check->close();
                break;
            }
            $stmt_check->close();

            $stmt = $conn->prepare("INSERT INTO categories (category_name, category_description) VALUES (?, ?)");
            $stmt->bind_param("ss", $category_name, $category_description);

            if ($stmt->execute()) {
                $category_id = $stmt->insert_id;
                http_response_code(201); // Created
                echo json_encode(['status' => 'success', 'message' => 'Category created successfully.', 'category_id' => $category_id]);
            } else {
                throw new Exception("Failed to create category: " . $stmt->error);
            }
            $stmt->close();
            break;

        case 'GET': // Read Category/Categories
            $category_id = sanitize_input($_GET['category_id'] ?? null);

            if ($category_id) {
                $stmt = $conn->prepare("SELECT category_id, category_name, category_description, created_at, updated_at FROM categories WHERE category_id = ?");
                $stmt->bind_param("i", $category_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $category = $result->fetch_assoc();
                $stmt->close();

                if ($category) {
                    echo json_encode(['status' => 'success', 'data' => $category]);
                } else {
                    http_response_code(404); // Not Found
                    echo json_encode(['status' => 'error', 'message' => 'Category not found.']);
                }
            } else {
                $result = $conn->query("SELECT category_id, category_name, category_description, created_at, updated_at FROM categories ORDER BY category_name ASC");
                $categories = [];
                while ($row = $result->fetch_assoc()) {
                    $categories[] = $row;
                }
                echo json_encode(['status' => 'success', 'data' => $categories]);
            }
            break;

        case 'PUT': // Update Category
            $data = json_decode(file_get_contents('php://input'), true);
             if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data for PUT request.']);
                break;
            }

            $category_id = sanitize_input($data['category_id'] ?? null);
            $category_name = sanitize_input($data['category_name'] ?? '');
            $category_description = sanitize_input($data['category_description'] ?? null);

            if (empty($category_id) || empty($category_name)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Category ID and name are required for update.']);
                break;
            }

            // Check if new category name already exists (and it's not the current category being updated)
            $stmt_check = $conn->prepare("SELECT category_id FROM categories WHERE category_name = ? AND category_id != ?");
            $stmt_check->bind_param("si", $category_name, $category_id);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                http_response_code(409); // Conflict
                echo json_encode(['status' => 'error', 'message' => 'Another category with this name already exists.']);
                $stmt_check->close();
                break;
            }
            $stmt_check->close();

            $stmt = $conn->prepare("UPDATE categories SET category_name = ?, category_description = ? WHERE category_id = ?");
            $stmt->bind_param("ssi", $category_name, $category_description, $category_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['status' => 'success', 'message' => 'Category updated successfully.']);
                } else {
                    // Potentially, no actual change made or category_id not found
                    http_response_code(404); // Or 200 if no change is not an error
                    echo json_encode(['status' => 'info', 'message' => 'No changes made or category not found.']);
                }
            } else {
                throw new Exception("Failed to update category: " . $stmt->error);
            }
            $stmt->close();
            break;

        case 'DELETE': // Delete Category
            // For DELETE, data often comes via query parameters or path, but can be in body too.
            // Assuming category_id comes from query parameter for simplicity here.
            // Or use $data = json_decode(file_get_contents('php://input'), true); if sent in body
            $category_id = sanitize_input($_GET['category_id'] ?? null);
             if (empty($category_id)) { // If from body: $category_id = sanitize_input($data['category_id'] ?? null);
                // Try reading from JSON body if not in query params
                $delete_data = json_decode(file_get_contents('php://input'), true);
                $category_id = sanitize_input($delete_data['category_id'] ?? null);
            }


            if (empty($category_id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Category ID is required for deletion.']);
                break;
            }

            // Optional: Check if category is in use by products
            $stmt_check = $conn->prepare("SELECT COUNT(*) as product_count FROM products WHERE category_id = ?");
            $stmt_check->bind_param("i", $category_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if ($result_check['product_count'] > 0) {
                http_response_code(409); // Conflict
                echo json_encode(['status' => 'error', 'message' => 'Cannot delete category. It is currently associated with ' . $result_check['product_count'] . ' product(s). Please reassign products before deleting.']);
                break;
            }

            $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
            $stmt->bind_param("i", $category_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully.']);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Category not found or already deleted.']);
                }
            } else {
                throw new Exception("Failed to delete category: " . $stmt->error);
            }
            $stmt->close();
            break;

        default:
            http_response_code(405); // Method Not Allowed
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("Categories API Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An internal server error occurred. Please try again later.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
