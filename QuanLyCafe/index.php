<?php
/**
 * VietAn Coffee Shop Management System
 *
 * Main entry point for the application
 */

// Start session
session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Include necessary files
require_once 'includes/functions.php';
require_once 'includes/access_control.php';

// Define routes
$routes = [
    '' => 'controllers/HomeController.php',
    'home' => 'controllers/HomeController.php',
    'login' => 'controllers/AuthController.php',
    'logout' => 'controllers/LogoutController.php',
    'dashboard' => 'controllers/DashboardController.php',
    'products' => 'controllers/ProductController.php',
    'customers' => 'controllers/CustomerController.php',
    'suppliers' => 'controllers/SupplierController.php',
    'ingredients' => 'controllers/IngredientController.php',
    'purchase-orders' => 'controllers/PurchaseOrderController.php',
    'sales-orders' => 'controllers/SalesOrderController.php',
    'invoices' => 'controllers/InvoiceController.php',
    'reports' => 'controllers/ReportController.php',
    'users' => 'controllers/UserController.php',
];

// Get the requested page from URL
$request = isset($_GET['page']) ? $_GET['page'] : '';

// Kiểm tra quyền truy cập dựa trên trang hiện tại
checkPageAccess($request);

// Check if the requested page exists in routes
if (array_key_exists($request, $routes)) {
    $controller_file = $routes[$request];

    // Check if controller file exists
    if (file_exists($controller_file)) {
        require_once $controller_file;
    } else {
        // Controller file not found, show 404 page
        header("HTTP/1.0 404 Not Found");
        require_once 'views/templates/404.php';
    }
} else {
    // Route not found, redirect to home
    header('Location: index.php?page=home');
    exit;
}