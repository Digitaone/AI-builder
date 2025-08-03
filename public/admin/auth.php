<?php
session_start();

// Check if the user is logged in. If not, redirect to the login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Include the database configuration file.
// The path is relative to the files in the admin directory.
require_once '../../src/config.php';
?>
