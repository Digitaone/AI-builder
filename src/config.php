<?php
/*
 * ------------------------------------------------------------------------------
 * Database Configuration
 * ------------------------------------------------------------------------------
 *
 * This file contains the configuration for the database connection.
 * Please update the credentials with your local database settings.
 *
 */

// --- Database Credentials ---
// Replace with your actual database server host (e.g., '127.0.0.1' or 'localhost')
define('DB_HOST', 'db');

// Replace with your database username
define('DB_USER', 'user');

// Replace with your database password
define('DB_PASS', 'password');

// Replace with the name of your database
define('DB_NAME', 'wanderchicvibes');


/*
 * ------------------------------------------------------------------------------
 * Establish Database Connection
 * ------------------------------------------------------------------------------
 *
 * Creates a new MySQLi object to connect to the database.
 * The connection object will be used throughout the application
 * to perform database queries.
 *
 */

// Create a new database connection instance
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    // If a connection error occurs, terminate the script and display the error.
    // In a production environment, you might want to log this error instead of
    // displaying it to the user.
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4 for full UTF-8 support
$conn->set_charset('utf8mb4');

?>
