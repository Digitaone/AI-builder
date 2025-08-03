<?php

/**
 * Renders a view template.
 *
 * @param string $template The name of the template file (without .php extension).
 * @param array $data Data to be extracted and made available to the view.
 */
function render_view($template, $data = []) {
    $view_path = __DIR__ . '/../templates/' . $template . '.php';

    if (file_exists($view_path)) {
        // Extract the data array to variables for the view
        extract($data);

        // Buffer output to prevent premature sending
        ob_start();
        include $view_path;
        ob_end_flush();
    } else {
        // In a real application, you might want to throw an exception or show a more detailed error.
        die("View '{$template}' not found.");
    }
}

/**
 * Checks if a user has purchased a specific product.
 *
 * @param PDO $pdo The database connection object.
 * @param int $user_id The ID of the user.
 * @param int $product_id The ID of the product.
 * @return bool True if the user has purchased the product, false otherwise.
 */
function user_has_purchased_product(PDO $pdo, int $user_id, int $product_id): bool {
    $sql = "SELECT COUNT(*)
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ?
            AND oi.product_id = ?
            AND o.status = 'completed'";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $product_id]);

    return $stmt->fetchColumn() > 0;
}

/**
 * Converts a string into a URL-friendly slug.
 *
 * @param string $string The string to slugify.
 * @return string The slugified string.
 */
function slugify(string $string): string {
    $string = preg_replace('~[^\pL\d]+~u', '-', $string);
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    $string = preg_replace('~[^-\w]+~', '', $string);
    $string = trim($string, '-');
    $string = preg_replace('~-+~', '-', $string);
    $string = strtolower($string);

    if (empty($string)) {
        return 'n-a';
    }

    return $string;
}
