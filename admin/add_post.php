<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("location: index.html");
}
include_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $body = mysqli_real_escape_string($conn, $_POST['body']);
    $author = $_SESSION['login_user'];

    $sql = "INSERT INTO posts (title, category, body, author) VALUES ('$title', '$category', '$body', '$author')";

    if (mysqli_query($conn, $sql)) {
        header("location: posts.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WanderChicVibes - Add Post</title>
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
        <h1 class="page-title">Add New Post</h1>
        <form action="add_post.php" method="POST">
            <div>
                <label>Title</label><br>
                <input type="text" name="title" placeholder="Title">
            </div>
            <div>
                <label>Category</label><br>
                <select name="category">
                    <option value="Fashion">Fashion</option>
                    <option value="Lifestyle">Lifestyle</option>
                    <option value="Travel">Travel</option>
                    <option value="Healthcare">Healthcare</option>
                    <option value="Technology">Technology</option>
                </select>
            </div>
            <div>
                <label>Body</label><br>
                <textarea name="body" placeholder="Body"></textarea>
            </div>
            <button class="button_1" type="submit">Submit</button>
        </form>
    </div>

    <footer>
        <p>WanderChicVibes &copy; 2024</p>
    </footer>
</body>
</html>
