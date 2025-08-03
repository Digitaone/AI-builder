<?php

/**
 * Displays the checkout page or redirects if user is not logged in or cart is empty.
 *
 * @param PDO $pdo The database connection object.
 */
function show_checkout_form(PDO $pdo) {
    // User must be logged in to check out
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login?redirect=checkout');
        exit;
    }

    // Cart must not be empty
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        header('Location: /cart');
        exit;
    }

    // --- Prepare Order Summary ---
    $cart_items = [];
    $total = 0;

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
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }

    render_view('checkout', [
        'cart_items' => $cart_items,
        'total' => $total
    ]);
}

/**
 * Processes the checkout form submission, creates an order, and simulates payment.
 *
 * @param PDO $pdo The database connection object.
 */
function process_order(PDO $pdo) {
    verify_csrf_token();
    // --- Security Checks ---
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /checkout');
        exit;
    }
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login?redirect=checkout');
        exit;
    }
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        header('Location: /cart');
        exit;
    }

    // --- Simulate Payment Processing ---
    // In a real app, this is where you would integrate with Stripe, PayPal, etc.
    // We'll assume the payment is always successful for this project.
    $payment_successful = true;

    if ($payment_successful) {
        // --- Calculate Total Again (server-side) ---
        $total = 0;

        $product_ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products_in_cart = $stmt->fetchAll();

        // Check if all products were found
        if (count($products_in_cart) !== count($product_ids)) {
            die('Error: One or more products in your cart could not be found.');
        }

        foreach ($products_in_cart as $product) {
            $total += $product['price'] * $cart[$product['id']];
        }

        // --- Save Order to Database ---
        try {
            $pdo->beginTransaction();

            // 1. Insert into 'orders' table
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)');
            $stmt->execute([$_SESSION['user_id'], $total, 'completed']);
            $order_id = $pdo->lastInsertId();

            // 2. Insert into 'order_items' table
            $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
            foreach ($products_in_cart as $product) {
                $quantity = $cart[$product['id']];
                $stmt->execute([$order_id, $product['id'], $quantity, $product['price']]);
            }

            $pdo->commit();

            // --- Clear Cart & Redirect to Success Page ---
            unset($_SESSION['cart']);
            header('Location: /order-success?order_id=' . $order_id);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            // In a real app, log the error and show an error page
            die('There was an error processing your order. Please try again. Error: ' . $e->getMessage());
        }
    } else {
        // If payment fails
        header('Location: /checkout?error=payment_failed');
        exit;
    }
}

/**
 * Displays the order success/confirmation page.
 */
function show_order_success() {
    render_view('order-success', [
        'order_id' => $_GET['order_id'] ?? null
    ]);
}
