<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("location: index.html");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WanderChicVibes - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><span class="highlight">WanderChic</span>Vibes Admin</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="posts.php">Posts</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Dashboard</h1>
        <p>Welcome to the admin panel. Here you can manage your website content.</p>
    </div>

    <footer>
        <p>WanderChicVibes &copy; 2024</p>
    </footer>
</body>
</html>
