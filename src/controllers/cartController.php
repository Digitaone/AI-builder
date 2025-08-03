<?php

/**
 * Displays the shopping cart page.
 *
 * @param PDO $pdo The database connection object.
 */
function show_cart(PDO $pdo) {
    $cart = $_SESSION['cart'] ?? [];
    $cart_items = [];
    $total = 0;

    if (!empty($cart)) {
        $product_ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll();

        foreach ($products as $product) {
            $quantity = $cart[$product['id']];
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            $cart_items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }

    render_view('cart', [
        'cart_items' => $cart_items,
        'total' => $total
    ]);
}

/**
 * Adds an item to the shopping cart.
 *
 * @param PDO $pdo The database connection object.
 */
function add_to_cart(PDO $pdo) {
    verify_csrf_token();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
        header('Location: /products');
        exit;
    }

    $product_id = (int)$_POST['product_id'];
    $quantity_to_add = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Check if product exists
    $stmt = $pdo->prepare('SELECT id FROM products WHERE id = ?');
    $stmt->execute([$product_id]);
    $product_exists = $stmt->fetch();

    if ($product_exists && $quantity_to_add > 0) {
        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        // Add or update product quantity
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity_to_add;
        } else {
            $_SESSION['cart'][$product_id] = $quantity_to_add;
        }
    }

    // Redirect to the cart page
    header('Location: /cart');
    exit;
}

/**
 * Updates the quantity of an item in the cart or removes it.
 */
function update_cart() {
    verify_csrf_token();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
        header('Location: /cart');
        exit;
    }

    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    if (isset($_SESSION['cart'][$product_id])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            // Remove item if quantity is 0 or less
            unset($_SESSION['cart'][$product_id]);
        }
    }

    header('Location: /cart');
    exit;
}

/**
 * Removes an item from the cart completely.
 */
function remove_from_cart() {
    verify_csrf_token();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
        header('Location: /cart');
        exit;
    }

    $product_id = (int)$_POST['product_id'];

    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }

    header('Location: /cart');
    exit;
}
