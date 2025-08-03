<?php

/**
 * Establishes a database connection.
 *
 * @return PDO The database connection object.
 */
function connect_db() {
    $config = require __DIR__ . '/../config/database.php';

    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

    try {
        $pdo = new PDO($dsn, $config['user'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        // In a real application, you would log this error and show a generic error message.
        die("Database connection failed: " . $e->getMessage());
    }
}
