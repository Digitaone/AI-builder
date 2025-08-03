<?php

/**
 * Displays the homepage with featured products.
 *
 * @param PDO $pdo The database connection object.
 */
function show_homepage(PDO $pdo) {
    // Fetch featured products from the database
    $stmt = $pdo->prepare('SELECT * FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT 4');
    $stmt->execute();
    $products = $stmt->fetchAll();

    $data = [
        'products' => $products,
        'title' => 'Digital Products Store - Ebooks, Software, and More',
        'meta_description' => 'Your one-stop shop for high-quality digital products. Browse our collection of ebooks, software, and templates.'
    ];

    render_view('home', $data);
}
