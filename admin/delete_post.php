<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("location: index.html");
}
include_once '../config.php';

$id = $_GET['id'];
$sql = "DELETE FROM posts WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    header("location: posts.php");
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
?>
