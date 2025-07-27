<?php
// This is a dummy test file.
// In a real-world scenario, you would use a testing framework like PHPUnit.

include_once '../config.php';

if ($conn) {
    echo "Database connection successful.\n";
} else {
    echo "Database connection failed.\n";
}
?>
