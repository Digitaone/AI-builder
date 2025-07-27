<?php
session_start();
include_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT id, password FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['login_user'] = $username;
        header("location: dashboard.php");
    } else {
        $error = "Your Login Name or Password is invalid";
        header("location: index.html?error=" . $error);
    }
}
?>
