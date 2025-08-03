<?php

/**
 * Displays the main admin dashboard.
 *
 * @param PDO $pdo The database connection object.
 */
function show_admin_dashboard(PDO $pdo) {
    // In the future, we can fetch stats here, e.g.,
    // $user_count = $pdo->query('SELECT count(*) FROM users')->fetchColumn();
    // $order_count = $pdo->query('SELECT count(*) FROM orders')->fetchColumn();

    $stats = [
        'user_count' => 0, // Placeholder
        'order_count' => 0, // Placeholder
        'total_sales' => 0 // Placeholder
    ];

    render_view('admin/dashboard', ['stats' => $stats]);
}

/**
 * Lists all products for the admin panel.
 *
 * @param PDO $pdo The database connection object.
 */
function admin_list_products(PDO $pdo) {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY created_at DESC');
    $products = $stmt->fetchAll();

    // Use a different render function for admin views to avoid path issues
    render_admin_view('products/index', ['products' => $products]);
}

/**
 * Renders an admin view template.
 * A helper function to avoid repeating the 'admin/' path.
 *
 * @param string $template The name of the template file.
 * @param array $data Data to be made available to the view.
 */
function render_admin_view($template, $data = []) {
    // This makes it so we don't have to include 'admin/partials/header.php'
    // It assumes a standard admin layout.
    render_view('admin/' . $template, $data);
}

/**
 * Shows the form for creating a new product.
 */
function admin_show_create_product_form() {
    render_admin_view('products/create');
}

/**
 * Handles the creation of a new product.
 *
 * @param PDO $pdo The database connection object.
 */
function admin_handle_create_product(PDO $pdo) {
    verify_csrf_token();
    $errors = [];
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $image = $_POST['image'] ?? '';
    $file_url = $_POST['file_url'] ?? '';
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Basic validation
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if (empty($price) || !is_numeric($price)) $errors[] = 'Price must be a valid number.';
    if (empty($image)) $errors[] = 'Image URL is required.';
    if (empty($file_url)) $errors[] = 'File URL is required.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO products (name, description, price, image, file_url, featured) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$name, $description, $price, $image, $file_url, $featured]);

            header('Location: /admin/products?success_message=Product created successfully!');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }

    // If there are errors, show the form again
    render_admin_view('products/create', ['errors' => $errors]);
}

/**
 * Shows the form for editing an existing product.
 *
 * @param PDO $pdo The database connection object.
 * @param int $id The ID of the product to edit.
 */
function admin_show_edit_product_form(PDO $pdo, int $id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        render_view('404');
        exit;
    }

    render_admin_view('products/edit', ['product' => $product]);
}

/**
 * Handles the update of an existing product.
 *
 * @param PDO $pdo The database connection object.
 * @param int $id The ID of the product to update.
 */
function admin_handle_edit_product(PDO $pdo, int $id) {
    verify_csrf_token();
    $errors = [];
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $image = $_POST['image'] ?? '';
    $file_url = $_POST['file_url'] ?? '';
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Basic validation
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if (empty($price) || !is_numeric($price)) $errors[] = 'Price must be a valid number.';
    if (empty($image)) $errors[] = 'Image URL is required.';
    if (empty($file_url)) $errors[] = 'File URL is required.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE products SET name = ?, description = ?, price = ?, image = ?, file_url = ?, featured = ? WHERE id = ?'
            );
            $stmt->execute([$name, $description, $price, $image, $file_url, $featured, $id]);

            header('Location: /admin/products?success_message=Product updated successfully!');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }

    // If there are errors, show the form again with the product data
    $_POST['id'] = $id; // Ensure ID is available in the view
    render_admin_view('products/edit', ['errors' => $errors, 'product' => $_POST]);
}

/**
 * Handles the deletion of a product.
 *
 * @param PDO $pdo The database connection object.
 */
function admin_handle_delete_product(PDO $pdo) {
    verify_csrf_token();
    if (!isset($_POST['product_id'])) {
        header('Location: /admin/products');
        exit;
    }

    $id = (int)$_POST['product_id'];

    try {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);

        header('Location: /admin/products?success_message=Product deleted successfully!');
        exit;
    } catch (PDOException $e) {
        header('Location: /admin/products?error_message=Error deleting product: ' . urlencode($e->getMessage()));
        exit;
    }
}

/**
 * Lists all orders for the admin panel.
 *
 * @param PDO $pdo The database connection object.
 */
function admin_list_orders(PDO $pdo) {
    $stmt = $pdo->query(
        'SELECT orders.*, users.username AS customer_name, users.email AS customer_email
         FROM orders
         JOIN users ON orders.user_id = users.id
         ORDER BY orders.created_at DESC'
    );
    $orders = $stmt->fetchAll();

    render_admin_view('orders/index', ['orders' => $orders]);
}

/**
 * Lists all users for the admin panel.
 *
 * @param PDO $pdo The database connection object.
 */
function admin_list_users(PDO $pdo) {
    $stmt = $pdo->query('SELECT id, username, email, is_admin, created_at FROM users ORDER BY created_at DESC');
    $users = $stmt->fetchAll();

    render_admin_view('users/index', ['users' => $users]);
}
