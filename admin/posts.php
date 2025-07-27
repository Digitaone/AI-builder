<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("location: index.html");
}
include_once '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WanderChicVibes - Manage Posts</title>
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
                    <li class="current"><a href="posts.php">Posts</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Manage Posts</h1>
        <a href="add_post.php" class="button_1">Add New Post</a>
        <table>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Author</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php
            $sql = "SELECT * FROM posts ORDER BY created_at DESC";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['category'] . "</td>";
                echo "<td>" . $row['author'] . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "<td><a href='edit_post.php?id=" . $row['id'] . "'>Edit</a> | <a href='delete_post.php?id=" . $row['id'] . "'>Delete</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <footer>
        <p>WanderChicVibes &copy; 2024</p>
    </footer>
</body>
</html>
