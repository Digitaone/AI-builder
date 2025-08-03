<?php

/**
 * Displays the product listing page.
 *
 * @param PDO $pdo The database connection object.
 */
function list_products(PDO $pdo) {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY created_at DESC');
    $products = $stmt->fetchAll();

    $data = [
        'products' => $products,
        'title' => 'All Products',
        'meta_description' => 'Browse our full collection of digital products.'
    ];

    render_view('products', $data);
}

/**
 * Displays the product detail page for a single product.
 *
 * @param PDO $pdo The database connection object.
 * @param int|null $id The ID of the product to display.
 */
function show_product(PDO $pdo, ?int $id) {
    if ($id === null) {
        http_response_code(404);
        render_view('404');
        return;
    }

    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        render_view('404');
        return;
    }

    // Generate a meta description from the product's description
    $meta_description = substr(strip_tags($product['description']), 0, 160);
    $meta_description = rtrim($meta_description, "!,.-") . '...';

    // Fetch reviews for the product
    $review_stmt = $pdo->prepare(
        'SELECT r.*, u.username
         FROM reviews r
         JOIN users u ON r.user_id = u.id
         WHERE r.product_id = ?
         ORDER BY r.created_at DESC'
    );
    $review_stmt->execute([$id]);
    $reviews = $review_stmt->fetchAll();

    // Calculate average rating
    $avg_rating = 0;
    if (!empty($reviews)) {
        $total_rating = 0;
        foreach ($reviews as $review) {
            $total_rating += $review['rating'];
        }
        $avg_rating = $total_rating / count($reviews);
    }

    // Check if the current user can leave a review
    $can_review = false;
    if (isset($_SESSION['user_id'])) {
        $can_review = user_has_purchased_product($pdo, $_SESSION['user_id'], $id);
    }

    render_view('product-detail', [
        'product' => $product,
        'reviews' => $reviews,
        'avg_rating' => $avg_rating,
        'can_review' => $can_review,
        'title' => htmlspecialchars($product['name']),
        'meta_description' => htmlspecialchars($meta_description)
    ]);
}
