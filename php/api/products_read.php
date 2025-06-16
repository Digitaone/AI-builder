<?php
header('Content-Type: application/json');
require_once '../includes/db_connect.php'; // Database connection

// Sanitize input function (basic example)
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    // Allow specific characters for search, but trim and prevent HTML injection.
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only GET is allowed.']);
    if (isset($conn) && $conn instanceof mysqli) { $conn->close(); }
    exit;
}

try {
    // --- Parameter Parsing & Validation ---
    $product_id = isset($_GET['product_id']) ? filter_var($_GET['product_id'], FILTER_VALIDATE_INT) : null;
    $category_id_filter = isset($_GET['category_id']) ? filter_var($_GET['category_id'], FILTER_VALIDATE_INT) : null;
    $search_term_filter = isset($_GET['search_term']) ? sanitize_input($_GET['search_term']) : null;

    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
    $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);
    $offset = ($page - 1) * $limit;

    $sort_by_input = isset($_GET['sort_by']) ? sanitize_input($_GET['sort_by']) : 'date_desc'; // Default sort

    // --- Sorting Logic ---
    $allowed_sort_options = [
        'price_asc' => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'name_asc' => 'p.product_name ASC',
        'name_desc' => 'p.product_name DESC',
        'date_desc' => 'p.created_at DESC',
        'date_asc' => 'p.created_at ASC',
        'rating_desc' => 'p.average_rating DESC, p.total_ratings DESC', // Example for rating
        'rating_asc' => 'p.average_rating ASC, p.total_ratings ASC',
    ];
    $order_by_clause = $allowed_sort_options[$sort_by_input] ?? $allowed_sort_options['date_desc']; // Fallback to default

    // --- SQL Construction ---
    $base_sql_select = "SELECT
                p.product_id, p.product_name, p.description, p.price,
                p.category_id, c.category_name,
                p.file_path, p.preview_path, p.cover_image_path,
                p.stock_available, p.average_rating, p.total_ratings,
                p.created_at, p.updated_at";
    $base_sql_from = " FROM products p LEFT JOIN categories c ON p.category_id = c.category_id";
    $base_sql_count_select = "SELECT COUNT(p.product_id) AS total_items";

    $where_clauses = [];
    $params = [];
    $types = "";

    if ($product_id) { // If fetching a single product, other filters/pagination/sorting are ignored
        $where_clauses[] = "p.product_id = ?";
        $params[] = $product_id;
        $types .= "i";
    } else {
        if ($category_id_filter) {
            $where_clauses[] = "p.category_id = ?";
            $params[] = $category_id_filter;
            $types .= "i";
        }
        if ($search_term_filter && strlen($search_term_filter) > 0) {
            $search_like = "%" . $search_term_filter . "%";
            // Adjust search fields as needed
            $where_clauses[] = "(p.product_name LIKE ? OR p.description LIKE ? OR c.category_name LIKE ?)";
            $params[] = $search_like;
            $params[] = $search_like;
            $params[] = $search_like;
            $types .= "sss";
        }
    }

    $where_sql = "";
    if (!empty($where_clauses)) {
        $where_sql = " WHERE " . implode(" AND ", $where_clauses);
    }

    // --- Total Count Query ---
    $total_items = 0;
    if (!$product_id) { // Only calculate total if not fetching a single product
        $count_sql = $base_sql_count_select . $base_sql_from . $where_sql;
        $stmt_count = $conn->prepare($count_sql);
        if (!$stmt_count) throw new Exception("Count SQL statement preparation failed: " . $conn->error);
        if (!empty($types)) $stmt_count->bind_param($types, ...$params); // Use same params as main query WHERE

        $stmt_count->execute();
        $count_result = $stmt_count->get_result()->fetch_assoc();
        $total_items = $count_result ? (int)$count_result['total_items'] : 0;
        $stmt_count->close();
    }


    // --- Main Data Query ---
    $data_sql = $base_sql_select . $base_sql_from . $where_sql;

    $main_query_params = $params; // Start with WHERE params
    $main_query_types = $types;

    if (!$product_id) { // Apply ordering and pagination only if not fetching a single product
        $data_sql .= " ORDER BY " . $order_by_clause; // $order_by_clause is from a safe map
        $data_sql .= " LIMIT ? OFFSET ?";
        $main_query_params[] = $limit;
        $main_query_params[] = $offset;
        $main_query_types .= "ii";
    }

    $stmt_data = $conn->prepare($data_sql);
    if (!$stmt_data) throw new Exception("Data SQL statement preparation failed: " . $conn->error);
    if (!empty($main_query_types)) $stmt_data->bind_param($main_query_types, ...$main_query_params);

    $stmt_data->execute();
    $result = $stmt_data->get_result();

    // --- Response Assembly ---
    if ($product_id) {
        $product = $result->fetch_assoc();
        if ($product) {
            echo json_encode(['status' => 'success', 'data' => $product]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        }
    } else {
        $products = [];
        while ($row = $result->fetch_assoc()) {
            // Ensure numeric types are correctly cast if necessary (PHP/MySQL often handles this)
            $row['price'] = (float)$row['price'];
            $row['average_rating'] = (float)$row['average_rating'];
            $row['total_ratings'] = (int)$row['total_ratings'];
            if ($row['stock_available'] !== null) $row['stock_available'] = (int)$row['stock_available'];
            $products[] = $row;
        }

        $total_pages = ($limit > 0 && $total_items > 0) ? ceil($total_items / $limit) : 0;
        if ($total_items == 0 && $page == 1) $total_pages = 1; // If no items, still 1 page
        if ($page > $total_pages && $total_items > 0) { // Requested page beyond actual number of pages
             http_response_code(404);
             echo json_encode([
                'status' => 'error',
                'message' => 'Page not found.',
                'products' => [], // Send empty products array
                'total_items' => $total_items,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => $total_pages
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'products' => $products,
                'total_items' => $total_items,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => $total_pages
            ]);
        }
    }
    $stmt_data->close();

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("Products Read API Error: " . $e->getMessage() . " SQL: " . ($data_sql ?? ($count_sql ?? "N/A")));
    echo json_encode(['status' => 'error', 'message' => 'An internal server error occurred. Please try again later.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
