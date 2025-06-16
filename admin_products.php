<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php?error=unauthorized_admin_products');
    exit;
}
include 'templates/header.php';
?>
<main>
    <h2>Manage Products</h2>

    <button id="showAddProductFormBtn" type="button">Add New Product</button>
    <div id="productFormContainer" style="display:none; margin-top: 20px; padding: 15px; border: 1px solid #ccc;">
        <h3 id="productFormTitle">Add New Product</h3>
        <form id="productForm" enctype="multipart/form-data">
            <input type="hidden" id="product_id" name="product_id">
            <div>
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            <div>
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Loading categories...</option>
                </select>
            </div>
            <div>
                <label for="stock_available">Stock Available (optional, leave empty for unlimited):</label>
                <input type="number" id="stock_available" name="stock_available" min="0">
            </div>
            <div>
                <label for="product_file">Product File (e.g., .zip, .pdf):</label>
                <input type="file" id="product_file" name="product_file">
                <small id="currentProductFile"></small>
            </div>
            <div>
                <label for="cover_image">Cover Image (e.g., .jpg, .png):</label>
                <input type="file" id="cover_image" name="cover_image" accept="image/*">
                <small id="currentCoverImage"></small>
            </div>
            <button type="submit" id="saveProductBtn">Save Product</button>
            <button type="button" id="cancelEditProductBtn" style="display:none;">Cancel Edit</button>
        </form>
        <div id="productFormMessages" style="margin-top: 10px;"></div>
    </div>

    <h3 style="margin-top: 30px;">Existing Products</h3>
    <div id="productsTableContainer">
        <p>Loading products...</p>
        <!-- Products will be listed here by js/admin.js -->
    </div>
</main>

<script src="js/admin.js"></script>
<?php include 'templates/footer.php'; ?>
