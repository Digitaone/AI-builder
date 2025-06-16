<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page with an error message
    header('Location: login.php?error=unauthorized_admin');
    exit;
}

include 'templates/header.php';
?>
<main>
    <h2>Admin Dashboard</h2>
    <p>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?>!</p>
    <p>This is the central hub for managing your digital store.</p>

    <h3>Core Management Areas:</h3>
    <ul>
        <li><a href="admin_products.php">Manage Products</a> - Add, edit, view, and delete products.</li>
        <li><a href="admin_categories.php">Manage Categories</a> - Organize your products by adding, editing, and deleting categories.</li>
        <!-- Add more links here as other admin features are developed, e.g.: -->
        <!-- <li><a href="admin_orders.php">Manage Orders</a></li> -->
        <!-- <li><a href="admin_users.php">Manage Users</a></li> -->
        <!-- <li><a href="admin_settings.php">Site Settings</a></li> -->
    </ul>

    <h3>Quick Stats (Placeholder):</h3>
    <div>
        <p>Total Products: <span id="statsTotalProducts">Loading...</span></p>
        <p>Total Categories: <span id="statsTotalCategories">Loading...</span></p>
        <p>Total Users: <span id="statsTotalUsers">Loading...</span></p>
        <p>Pending Orders: <span id="statsPendingOrders">Loading...</span></p>
    </div>

    <p><a href="index.php">Back to Store Front</a></p>

</main>

<?php
// Optional: Include admin-specific JS for dashboard if needed, or rely on main.js for nav.
// For now, specific admin page JS will be linked on those pages.
include 'templates/footer.php';
?>
