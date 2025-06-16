<?php
// Database connection configuration
// PLEASE UPDATE THESE PLACEHOLDER CREDENTIALS WITH YOUR ACTUAL DATABASE DETAILS
$servername = "localhost"; // Database server hostname (e.g., "localhost" or IP address)
$username = "your_db_username"; // Your MySQL database username
$password = "your_db_password"; // Your MySQL database password
$dbname = "digital_store_db";   // The name of your database

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log error to a file or error tracking system in a production environment
    // For development, die() is okay, but avoid in production for security reasons.
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed. Please try again later or contact support."); // User-friendly message
}

// Set character set to utf8mb4 for full Unicode support
if (!$conn->set_charset("utf8mb4")) {
    // Log error if setting charset fails
    error_log("Error loading character set utf8mb4: " . $conn->error);
    // Optionally, you could decide to die() here as well if charset is critical
}

// Optional: Echo for testing connection during development
// Remove or comment out in production
// echo "Connected successfully to database: " . $dbname;

// The $conn variable can now be used throughout the application to interact with the database.
// For example, in other PHP files, you would include this file:
// require_once 'db_connect.php';
// And then use $conn like: $conn->query("SELECT * FROM users");

?>
