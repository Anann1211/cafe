<?php
/**
 * Dashboard Controller
 *
 * Handles dashboard functionality
 */

// Include database connection
$conn = require_once 'config/database.php';

// Get today's date
$today = date('Y-m-d');
$current_month = date('Y-m');
$current_year = date('Y');

// Get total sales for today
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM sales_orders WHERE DATE(order_date) = ? AND status = 'completed'");
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$today_sales = $result->fetch_assoc()['total'];
$stmt->close();

// Get total sales for current month
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM sales_orders WHERE DATE_FORMAT(order_date, '%Y-%m') = ? AND status = 'completed'");
$stmt->bind_param("s", $current_month);
$stmt->execute();
$result = $stmt->get_result();
$month_sales = $result->fetch_assoc()['total'];
$stmt->close();

// Get total sales for current year
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM sales_orders WHERE YEAR(order_date) = ? AND status = 'completed'");
$stmt->bind_param("s", $current_year);
$stmt->execute();
$result = $stmt->get_result();
$year_sales = $result->fetch_assoc()['total'];
$stmt->close();

// Get total number of products
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM products");
$stmt->execute();
$result = $stmt->get_result();
$total_products = $result->fetch_assoc()['total'];
$stmt->close();

// Get total number of customers
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM customers");
$stmt->execute();
$result = $stmt->get_result();
$total_customers = $result->fetch_assoc()['total'];
$stmt->close();

// Get total number of suppliers
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM suppliers");
$stmt->execute();
$result = $stmt->get_result();
$total_suppliers = $result->fetch_assoc()['total'];
$stmt->close();

// Get total number of ingredients
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM ingredients");
$stmt->execute();
$result = $stmt->get_result();
$total_ingredients = $result->fetch_assoc()['total'];
$stmt->close();

// Get recent sales
$stmt = $conn->prepare("
    SELECT so.id, so.order_number, so.order_date, so.total_amount, c.name as customer_name, u.name as user_name
    FROM sales_orders so
    LEFT JOIN customers c ON so.customer_id = c.id
    LEFT JOIN users u ON so.user_id = u.id
    WHERE so.status = 'completed'
    ORDER BY so.order_date DESC
    LIMIT 5
");
$stmt->execute();
$recent_sales = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get recent purchases
$stmt = $conn->prepare("
    SELECT po.id, po.order_number, po.order_date, po.total_amount, s.name as supplier_name, u.name as user_name
    FROM purchase_orders po
    LEFT JOIN suppliers s ON po.supplier_id = s.id
    LEFT JOIN users u ON po.user_id = u.id
    WHERE po.status = 'completed'
    ORDER BY po.order_date DESC
    LIMIT 5
");
$stmt->execute();
$recent_purchases = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get low stock products
$stmt = $conn->prepare("
    SELECT id, name, code, stock_quantity
    FROM products
    WHERE stock_quantity <= 10
    ORDER BY stock_quantity ASC
    LIMIT 5
");
$stmt->execute();
$low_stock_products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get low stock ingredients
$stmt = $conn->prepare("
    SELECT id, name, unit, stock_quantity
    FROM ingredients
    WHERE stock_quantity <= 10
    ORDER BY stock_quantity ASC
    LIMIT 5
");
$stmt->execute();
$low_stock_ingredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Include dashboard view
include 'views/dashboard/index.php';
