<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("location: index.html");
}
include_once '../config.php';

$id = $_GET['id'];
$sql = "SELECT * FROM posts WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $body = mysqli_real_escape_string($conn, $_POST['body']);

    $sql = "UPDATE posts SET title = '$title', category = '$category', body = '$body' WHERE id = $id";

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
    <title>WanderChicVibes - Edit Post</title>
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
        <h1 class="page-title">Edit Post</h1>
        <form action="edit_post.php?id=<?php echo $id; ?>" method="POST">
            <div>
                <label>Title</label><br>
                <input type="text" name="title" value="<?php echo $row['title']; ?>">
            </div>
            <div>
                <label>Category</label><br>
                <select name="category">
                    <option value="Fashion" <?php if($row['category'] == 'Fashion') echo 'selected'; ?>>Fashion</option>
                    <option value="Lifestyle" <?php if($row['category'] == 'Lifestyle') echo 'selected'; ?>>Lifestyle</option>
                    <option value="Travel" <?php if($row['category'] == 'Travel') echo 'selected'; ?>>Travel</option>
                    <option value="Healthcare" <?php if($row['category'] == 'Healthcare') echo 'selected'; ?>>Healthcare</option>
                    <option value="Technology" <?php if($row['category'] == 'Technology') echo 'selected'; ?>>Technology</option>
                </select>
            </div>
            <div>
                <label>Body</label><br>
                <textarea name="body"><?php echo $row['body']; ?></textarea>
            </div>
            <button class="button_1" type="submit">Submit</button>
        </form>
    </div>

    <footer>
        <p>WanderChicVibes &copy; 2024</p>
    </footer>
</body>
</html>
