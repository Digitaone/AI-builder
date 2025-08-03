<?php

// Start session
session_start();

// Include core files
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';

// Connect to the database
$pdo = connect_db();

// Simple router
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = ''; // Adjust if your project is in a subfolder
$route = str_replace($base_path, '', $request_uri);
$route = trim($route, '/');
$route = parse_url($route, PHP_URL_PATH);

// Load controllers
require_once __DIR__ . '/../src/controllers/homeController.php';
require_once __DIR__ . '/../src/controllers/productController.php';
require_once __DIR__ . '/../src/controllers/authController.php';
require_once __DIR__ . '/../src/controllers/cartController.php';
require_once __DIR__ . '/../src/controllers/checkoutController.php';
require_once __DIR__ . '/../src/controllers/adminController.php';
require_once __DIR__ . '/../src/controllers/reviewController.php';
require_once __DIR__ . '/../src/controllers/contactController.php';
require_once __DIR__ . '/../src/auth.php';

// Define routes
// A more advanced router would handle request methods and regex, but this is fine for now.
$parts = explode('/', $route);

if ($route === '' || $route === 'home') {
    show_homepage($pdo);
} elseif ($route === 'products') {
    list_products($pdo);
} elseif ($parts[0] === 'product' && isset($parts[1])) {
    $id = (int)$parts[1];
    show_product($pdo, $id);
} elseif ($route === 'register') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handle_registration($pdo);
        } else {
            show_register_form();
} elseif ($route === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handle_login($pdo);
    } else {
        show_login_form();
    }
} elseif ($route === 'logout') {
    logout();
} elseif ($route === 'cart') {
    show_cart($pdo);
} elseif ($route === 'cart/add') {
    add_to_cart($pdo);
} elseif ($route === 'cart/update') {
    update_cart();
} elseif ($route === 'cart/remove') {
    remove_from_cart();
} elseif ($route === 'checkout') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        process_order($pdo);
    } else {
        show_checkout_form($pdo);
    }
} elseif ($route === 'order-success') {
    show_order_success();
} elseif ($route === 'contact') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handle_contact_form();
    } else {
        show_contact_form();
    }
} elseif ($route === 'reviews/create') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handle_create_review($pdo);
    } else {
        header('Location: /');
    }
} elseif ($parts[0] === 'admin') {
    require_admin();
    $admin_route = $parts[1] ?? 'dashboard';

    if ($admin_route === 'dashboard') {
        show_admin_dashboard($pdo);
    } elseif ($admin_route === 'products') {
        if (isset($parts[2])) {
            $action = $parts[2];
            if ($action === 'create') {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    admin_handle_create_product($pdo);
                } else {
                    admin_show_create_product_form();
                }
            } elseif ($action === 'edit' && isset($_GET['id'])) {
                 $id = (int)$_GET['id'];
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    admin_handle_edit_product($pdo, $id);
                } else {
                    admin_show_edit_product_form($pdo, $id);
                }
            } elseif ($action === 'delete') {
                 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    admin_handle_delete_product($pdo);
                } else {
                    header('Location: /admin/products');
                }
            }
        } else {
            admin_list_products($pdo);
        }
    } elseif ($admin_route === 'orders') {
        admin_list_orders($pdo);
    } elseif ($admin_route === 'users') {
        admin_list_users($pdo);
    } else {
        http_response_code(404);
        render_view('404');
    }
} else {
    http_response_code(404);
    render_view('404');
}
