<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php?error=unauthorized_admin_categories');
    exit;
}
include 'templates/header.php';
?>
<main>
    <h2>Manage Categories</h2>

    <button id="showAddCategoryFormBtn" type="button">Add New Category</button>
    <div id="categoryFormContainer" style="display:none; margin-top: 20px; padding: 15px; border: 1px solid #ccc;">
        <h3 id="categoryFormTitle">Add New Category</h3>
        <form id="categoryForm">
            <input type="hidden" id="category_id_form" name="category_id"> <!-- Changed id to avoid conflict -->
            <div>
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>
            <div>
                <label for="category_description">Description (Optional):</label>
                <textarea id="category_description" name="category_description" rows="3"></textarea>
            </div>
            <button type="submit" id="saveCategoryBtn">Save Category</button>
            <button type="button" id="cancelEditCategoryBtn" style="display:none;">Cancel Edit</button>
        </form>
        <div id="categoryFormMessages" style="margin-top: 10px;"></div>
    </div>

    <h3 style="margin-top: 30px;">Existing Categories</h3>
    <div id="categoriesTableContainer">
        <p>Loading categories...</p>
        <!-- Categories will be listed here by js/admin.js -->
    </div>
</main>

<script src="js/admin.js"></script>
<?php include 'templates/footer.php'; ?>
