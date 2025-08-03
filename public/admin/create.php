<?php
require_once 'auth.php'; // Auth check and DB connection

$title = $content = $category = $author_name = $featured_image_url = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = trim($_POST['category']);
    $author_name = trim($_POST['author_name']);
    $featured_image_url = trim($_POST['featured_image_url']);

    if (empty($title)) { $errors[] = 'Title is required.'; }
    if (empty($content)) { $errors[] = 'Content is required.'; }
    if (empty($category)) { $errors[] = 'Category is required.'; }
    if (empty($author_name)) { $errors[] = 'Author name is required.'; }
    if (!empty($featured_image_url) && !filter_var($featured_image_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'Featured image URL is not valid.';
    }

    if (empty($errors)) {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO posts (title, content, category, author_name, featured_image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $content, $category, $author_name, $featured_image_url);

        if ($stmt->execute()) {
            header("Location: index.php?message=success");
            exit;
        } else {
            $errors[] = "Error creating post: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-6">Create New Post</h1>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="create.php" method="POST" class="bg-white p-8 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="content" class="block text-gray-700 font-bold mb-2">Content</label>
                <textarea id="content" name="content" rows="10" class="w-full px-3 py-2 border rounded" required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="category" class="block text-gray-700 font-bold mb-2">Category</label>
                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="author_name" class="block text-gray-700 font-bold mb-2">Author Name</label>
                <input type="text" id="author_name" name="author_name" value="<?php echo htmlspecialchars($author_name); ?>" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="featured_image_url" class="block text-gray-700 font-bold mb-2">Featured Image URL</label>
                <input type="url" id="featured_image_url" name="featured_image_url" value="<?php echo htmlspecialchars($featured_image_url); ?>" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create Post</button>
                <a href="index.php" class="text-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
