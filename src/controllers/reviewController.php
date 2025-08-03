<?php

/**
 * Handles the creation of a new product review.
 *
 * @param PDO $pdo The database connection object.
 */
function handle_create_review(PDO $pdo) {
    verify_csrf_token();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /');
        exit;
    }

    // User must be logged in
    if (!isset($_SESSION['user_id'])) {
        // Or redirect to login page
        http_response_code(403);
        die('You must be logged in to leave a review.');
    }

    $product_id = $_POST['product_id'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Validation
    if (empty($product_id) || empty($rating)) {
        // Redirect back with an error
        header('Location: /product?id=' . $product_id . '&error=rating_required');
        exit;
    }
    if ($rating < 1 || $rating > 5) {
        header('Location: /product?id=' . $product_id . '&error=invalid_rating');
        exit;
    }

    // Verify user has purchased the product
    if (!user_has_purchased_product($pdo, $user_id, $product_id)) {
        header('Location: /product?id=' . $product_id . '&error=not_purchased');
        exit;
    }

    // Prevent duplicate reviews
    $stmt = $pdo->prepare('SELECT id FROM reviews WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$user_id, $product_id]);
    if ($stmt->fetch()) {
        header('Location: /product?id=' . $product_id . '&error=already_reviewed');
        exit;
    }

    // Insert the review
    try {
        $stmt = $pdo->prepare('INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)');
        $stmt->execute([$product_id, $user_id, $rating, $comment]);

        header('Location: /product?id=' . $product_id . '&success=review_submitted');
        exit;
    } catch (PDOException $e) {
        header('Location: /product?id=' . $product_id . '&error=database_error');
        exit;
    }
}
