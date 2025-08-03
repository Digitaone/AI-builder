<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $title ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="/assets/css/admin_style.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="/admin/dashboard">Dashboard</a></li>
                    <li><a href="/admin/products">Products</a></li>
                    <li><a href="/admin/orders">Orders</a></li>
                    <li><a href="/admin/users">Users</a></li>
                    <li><a href="/">View Site</a></li>
                    <li><a href="/logout">Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="main-header">
                <h1><?php echo $title ?? 'Dashboard'; ?></h1>
            </header>
            <div class="content-body">
