<?php
require_once 'auth.php'; // Auth check and DB connection

$id = $_GET['id'] ?? $_POST['id'] ?? 0;
if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
    header('Location: index.php?error=invalid_id');
    exit;
}

$title = $content = $category = $author_name = $featured_image_url = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- UPDATE LOGIC ---
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = trim($_POST['category']);
    $author_name = trim($_POST['author_name']);
    $featured_image_url = trim($_POST['featured_image_url']);

    if (empty($title)) { $errors[] = 'Title is required.'; }
    // ... other validations ...

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, category = ?, author_name = ?, featured_image_url = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $title, $content, $category, $author_name, $featured_image_url, $id);

        if ($stmt->execute()) {
            header("Location: index.php?message=updated");
            exit;
        } else {
            $errors[] = "Error updating post: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    // --- FETCH LOGIC ---
    $stmt = $conn->prepare("SELECT title, content, category, author_name, featured_image_url FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $post = $result->fetch_assoc();
        $title = $post['title'];
        $content = $post['content'];
        $category = $post['category'];
        $author_name = $post['author_name'];
        $featured_image_url = $post['featured_image_url'];
    } else {
        header('Location: index.php?error=not_found');
        exit;
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-6">Edit Post</h1>

        <?php if (!empty($errors)): ?>
            <!-- Error display -->
        <?php endif; ?>

        <form action="edit.php" method="POST" class="bg-white p-8 rounded-lg shadow-md">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

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
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Post</button>
                <a href="index.php" class="text-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
