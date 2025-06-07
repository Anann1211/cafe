<?php
/**
 * Report Controller
 * 
 * Quản lý báo cáo và thống kê
 */

// Kiểm tra quyền admin
checkAdminAccess();

// Kết nối database
$conn = require_once 'config/database.php';

// Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Xử lý các action
switch ($action) {
    case 'sales':
        // Lấy thông tin thời gian từ form
        $date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : date('Y-m-01'); // Mặc định là ngày đầu tháng
        $date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : date('Y-m-d'); // Mặc định là ngày hiện tại
        $group_by = isset($_GET['group_by']) ? sanitize($_GET['group_by']) : 'day'; // Mặc định nhóm theo ngày
        
        // Truy vấn dữ liệu doanh thu
        $query = "SELECT ";
        
        // Nhóm theo ngày, tháng hoặc năm
        switch ($group_by) {
            case 'day':
                $query .= "DATE(so.order_date) as date, ";
                break;
            case 'month':
                $query .= "DATE_FORMAT(so.order_date, '%Y-%m') as date, ";
                break;
            case 'year':
                $query .= "YEAR(so.order_date) as date, ";
                break;
            default:
                $query .= "DATE(so.order_date) as date, ";
        }
        
        $query .= "COUNT(so.id) as order_count, 
                  SUM(so.total_amount) as total_sales 
                  FROM sales_orders so 
                  WHERE so.status = 'completed' 
                  AND so.order_date BETWEEN ? AND ? 
                  GROUP BY date 
                  ORDER BY date ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $date_from, $date_to);
        $stmt->execute();
        $result = $stmt->get_result();
        $sales_data = $result->fetch_all(MYSQLI_ASSOC);
        
        // Tính tổng doanh thu
        $total_sales = 0;
        $total_orders = 0;
        foreach ($sales_data as $data) {
            $total_sales += $data['total_sales'];
            $total_orders += $data['order_count'];
        }
        
        // Truy vấn top 5 sản phẩm bán chạy
        $top_products_query = "SELECT p.id, p.name, p.code, SUM(soi.quantity) as total_quantity, 
                              SUM(soi.total_price) as total_sales 
                              FROM sales_order_items soi 
                              JOIN products p ON soi.product_id = p.id 
                              JOIN sales_orders so ON soi.sales_order_id = so.id 
                              WHERE so.status = 'completed' 
                              AND so.order_date BETWEEN ? AND ? 
                              GROUP BY p.id 
                              ORDER BY total_quantity DESC 
                              LIMIT 5";
        
        $stmt = $conn->prepare($top_products_query);
        $stmt->bind_param("ss", $date_from, $date_to);
        $stmt->execute();
        $result = $stmt->get_result();
        $top_products = $result->fetch_all(MYSQLI_ASSOC);
        
        // Hiển thị báo cáo doanh thu
        include 'views/reports/sales.php';
        break;
    
    case 'expenses':
        // Lấy thông tin thời gian từ form
        $date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : date('Y-m-01'); // Mặc định là ngày đầu tháng
        $date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : date('Y-m-d'); // Mặc định là ngày hiện tại
        $group_by = isset($_GET['group_by']) ? sanitize($_GET['group_by']) : 'day'; // Mặc định nhóm theo ngày
        
        // Truy vấn dữ liệu chi phí
        $query = "SELECT ";
        
        // Nhóm theo ngày, tháng hoặc năm
        switch ($group_by) {
            case 'day':
                $query .= "DATE(po.order_date) as date, ";
                break;
            case 'month':
                $query .= "DATE_FORMAT(po.order_date, '%Y-%m') as date, ";
                break;
            case 'year':
                $query .= "YEAR(po.order_date) as date, ";
                break;
            default:
                $query .= "DATE(po.order_date) as date, ";
        }
        
        $query .= "COUNT(po.id) as order_count, 
                  SUM(po.total_amount) as total_expenses 
                  FROM purchase_orders po 
                  WHERE po.status = 'completed' 
                  AND po.order_date BETWEEN ? AND ? 
                  GROUP BY date 
                  ORDER BY date ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $date_from, $date_to);
        $stmt->execute();
        $result = $stmt->get_result();
        $expenses_data = $result->fetch_all(MYSQLI_ASSOC);
        
        // Tính tổng chi phí
        $total_expenses = 0;
        $total_orders = 0;
        foreach ($expenses_data as $data) {
            $total_expenses += $data['total_expenses'];
            $total_orders += $data['order_count'];
        }
        
        // Truy vấn top 5 nguyên liệu nhập nhiều nhất
        $top_ingredients_query = "SELECT i.id, i.name, i.unit, SUM(poi.quantity) as total_quantity, 
                                 SUM(poi.total_price) as total_expenses 
                                 FROM purchase_order_items poi 
                                 JOIN ingredients i ON poi.ingredient_id = i.id 
                                 JOIN purchase_orders po ON poi.purchase_order_id = po.id 
                                 WHERE po.status = 'completed' 
                                 AND po.order_date BETWEEN ? AND ? 
                                 GROUP BY i.id 
                                 ORDER BY total_quantity DESC 
                                 LIMIT 5";
        
        $stmt = $conn->prepare($top_ingredients_query);
        $stmt->bind_param("ss", $date_from, $date_to);
        $stmt->execute();
        $result = $stmt->get_result();
        $top_ingredients = $result->fetch_all(MYSQLI_ASSOC);
        
        // Hiển thị báo cáo chi phí
        include 'views/reports/expenses.php';
        break;
    
    case 'inventory':
        // Lấy danh sách nguyên liệu tồn kho
        $ingredients_query = "SELECT i.*, s.name as supplier_name 
                             FROM ingredients i 
                             LEFT JOIN suppliers s ON i.supplier_id = s.id 
                             ORDER BY i.stock_quantity ASC";
        
        $result = $conn->query($ingredients_query);
        $ingredients = $result->fetch_all(MYSQLI_ASSOC);
        
        // Tính tổng giá trị tồn kho nguyên liệu
        $total_inventory_value = 0;
        foreach ($ingredients as $ingredient) {
            $total_inventory_value += $ingredient['stock_quantity'] * $ingredient['price_per_unit'];
        }
        
        // Lấy danh sách sản phẩm tồn kho
        $products_query = "SELECT * FROM products ORDER BY stock_quantity ASC";
        $result = $conn->query($products_query);
        $products = $result->fetch_all(MYSQLI_ASSOC);
        
        // Tính tổng giá trị tồn kho sản phẩm
        $total_product_value = 0;
        foreach ($products as $product) {
            $total_product_value += $product['stock_quantity'] * $product['price'];
        }
        
        // Hiển thị báo cáo tồn kho
        include 'views/reports/inventory.php';
        break;
    
    case 'profit':
        // Lấy thông tin thời gian từ form
        $date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : date('Y-m-01'); // Mặc định là ngày đầu tháng
        $date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : date('Y-m-d'); // Mặc định là ngày hiện tại
        $group_by = isset($_GET['group_by']) ? sanitize($_GET['group_by']) : 'day'; // Mặc định nhóm theo ngày
        
        // Truy vấn dữ liệu doanh thu
        $sales_query = "SELECT ";
        
        // Nhóm theo ngày, tháng hoặc năm
        switch ($group_by) {
            case 'day':
                $sales_query .= "DATE(so.order_date) as date, ";
                break;
            case 'month':
                $sales_query .= "DATE_FORMAT(so.order_date, '%Y-%m') as date, ";
                break;
            case 'year':
                $sales_query .= "YEAR(so.order_date) as date, ";
                break;
            default:
                $sales_query .= "DATE(so.order_date) as date, ";
        }
        
        $sales_query .= "SUM(so.total_amount) as total_sales 
                        FROM sales_orders so 
                        WHERE so.status = 'completed' 
                        AND so.order_date BETWEEN ? AND ? 
                        GROUP BY date 
                        ORDER BY date ASC";
        
        $stmt = $conn->prepare($sales_query);
        $stmt->bind_param("ss", $date_from, $date_to);
        $stmt->execute();
        $result = $stmt->get_result();
        $sales_data = $result->fetch_all(MYSQLI_ASSOC);
        
        // Truy vấn dữ liệu chi phí
        $expenses_query = "SELECT ";
        
        // Nhóm theo ngày, tháng hoặc năm
        switch ($group_by) {
            case 'day':
                $expenses_query .= "DATE(po.order_date) as date, ";
                break;
            case 'month':
                $expenses_query .= "DATE_FORMAT(po.order_date, '%Y-%m') as date, ";
                break;
            case 'year':
                $expenses_query .= "YEAR(po.order_date) as date, ";
                break;
            default:
                $expenses_query .= "DATE(po.order_date) as date, ";
        }
        
        $expenses_query .= "SUM(po.total_amount) as total_expenses 
                           FROM purchase_orders po 
                           WHERE po.status = 'completed' 
                           AND po.order_date BETWEEN ? AND ? 
                           GROUP BY date 
                           ORDER BY date ASC";
        
        $stmt = $conn->prepare($expenses_query);
        $stmt->bind_param("ss", $date_from, $date_to);
        $stmt->execute();
        $result = $stmt->get_result();
        $expenses_data = $result->fetch_all(MYSQLI_ASSOC);
        
        // Kết hợp dữ liệu doanh thu và chi phí
        $profit_data = [];
        
        // Thêm dữ liệu doanh thu
        foreach ($sales_data as $data) {
            $date = $data['date'];
            if (!isset($profit_data[$date])) {
                $profit_data[$date] = [
                    'date' => $date,
                    'sales' => 0,
                    'expenses' => 0,
                    'profit' => 0
                ];
            }
            $profit_data[$date]['sales'] = $data['total_sales'];
        }
        
        // Thêm dữ liệu chi phí
        foreach ($expenses_data as $data) {
            $date = $data['date'];
            if (!isset($profit_data[$date])) {
                $profit_data[$date] = [
                    'date' => $date,
                    'sales' => 0,
                    'expenses' => 0,
                    'profit' => 0
                ];
            }
            $profit_data[$date]['expenses'] = $data['total_expenses'];
        }
        
        // Tính lợi nhuận
        $total_sales = 0;
        $total_expenses = 0;
        $total_profit = 0;
        
        foreach ($profit_data as &$data) {
            $data['profit'] = $data['sales'] - $data['expenses'];
            $total_sales += $data['sales'];
            $total_expenses += $data['expenses'];
            $total_profit += $data['profit'];
        }
        
        // Sắp xếp dữ liệu theo ngày
        ksort($profit_data);
        
        // Hiển thị báo cáo lợi nhuận
        include 'views/reports/profit.php';
        break;
    
    case 'export':
        // Lấy loại báo cáo từ URL
        $report_type = isset($_GET['type']) ? sanitize($_GET['type']) : '';
        $date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : date('Y-m-01');
        $date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : date('Y-m-d');
        
        // Tạo tên file
        $filename = $report_type . '_report_' . date('Ymd') . '.csv';
        
        // Thiết lập header cho file CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Mở output stream
        $output = fopen('php://output', 'w');
        
        // Thêm BOM (Byte Order Mark) để Excel hiển thị đúng tiếng Việt
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Xuất dữ liệu tùy theo loại báo cáo
        switch ($report_type) {
            case 'sales':
                // Tiêu đề cột
                fputcsv($output, ['Ngày', 'Số đơn hàng', 'Tổng doanh thu']);
                
                // Truy vấn dữ liệu
                $query = "SELECT DATE(so.order_date) as date, COUNT(so.id) as order_count, 
                         SUM(so.total_amount) as total_sales 
                         FROM sales_orders so 
                         WHERE so.status = 'completed' 
                         AND so.order_date BETWEEN ? AND ? 
                         GROUP BY date 
                         ORDER BY date ASC";
                
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $date_from, $date_to);
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Xuất dữ liệu
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, [
                        $row['date'],
                        $row['order_count'],
                        $row['total_sales']
                    ]);
                }
                break;
            
            case 'expenses':
                // Tiêu đề cột
                fputcsv($output, ['Ngày', 'Số đơn nhập hàng', 'Tổng chi phí']);
                
                // Truy vấn dữ liệu
                $query = "SELECT DATE(po.order_date) as date, COUNT(po.id) as order_count, 
                         SUM(po.total_amount) as total_expenses 
                         FROM purchase_orders po 
                         WHERE po.status = 'completed' 
                         AND po.order_date BETWEEN ? AND ? 
                         GROUP BY date 
                         ORDER BY date ASC";
                
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $date_from, $date_to);
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Xuất dữ liệu
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, [
                        $row['date'],
                        $row['order_count'],
                        $row['total_expenses']
                    ]);
                }
                break;
            
            case 'inventory':
                // Tiêu đề cột
                fputcsv($output, ['ID', 'Tên', 'Đơn vị', 'Tồn kho', 'Giá/Đơn vị', 'Giá trị', 'Nhà cung cấp']);
                
                // Truy vấn dữ liệu
                $query = "SELECT i.*, s.name as supplier_name 
                         FROM ingredients i 
                         LEFT JOIN suppliers s ON i.supplier_id = s.id 
                         ORDER BY i.name ASC";
                
                $result = $conn->query($query);
                
                // Xuất dữ liệu
                while ($row = $result->fetch_assoc()) {
                    $value = $row['stock_quantity'] * $row['price_per_unit'];
                    fputcsv($output, [
                        $row['id'],
                        $row['name'],
                        $row['unit'],
                        $row['stock_quantity'],
                        $row['price_per_unit'],
                        $value,
                        $row['supplier_name']
                    ]);
                }
                break;
            
            case 'profit':
                // Tiêu đề cột
                fputcsv($output, ['Ngày', 'Doanh thu', 'Chi phí', 'Lợi nhuận']);
                
                // Truy vấn dữ liệu doanh thu
                $sales_query = "SELECT DATE(so.order_date) as date, SUM(so.total_amount) as total_sales 
                               FROM sales_orders so 
                               WHERE so.status = 'completed' 
                               AND so.order_date BETWEEN ? AND ? 
                               GROUP BY date";
                
                $stmt = $conn->prepare($sales_query);
                $stmt->bind_param("ss", $date_from, $date_to);
                $stmt->execute();
                $sales_result = $stmt->get_result();
                $sales_data = [];
                
                while ($row = $sales_result->fetch_assoc()) {
                    $sales_data[$row['date']] = $row['total_sales'];
                }
                
                // Truy vấn dữ liệu chi phí
                $expenses_query = "SELECT DATE(po.order_date) as date, SUM(po.total_amount) as total_expenses 
                                  FROM purchase_orders po 
                                  WHERE po.status = 'completed' 
                                  AND po.order_date BETWEEN ? AND ? 
                                  GROUP BY date";
                
                $stmt = $conn->prepare($expenses_query);
                $stmt->bind_param("ss", $date_from, $date_to);
                $stmt->execute();
                $expenses_result = $stmt->get_result();
                $expenses_data = [];
                
                while ($row = $expenses_result->fetch_assoc()) {
                    $expenses_data[$row['date']] = $row['total_expenses'];
                }
                
                // Kết hợp dữ liệu
                $dates = array_unique(array_merge(array_keys($sales_data), array_keys($expenses_data)));
                sort($dates);
                
                // Xuất dữ liệu
                foreach ($dates as $date) {
                    $sales = isset($sales_data[$date]) ? $sales_data[$date] : 0;
                    $expenses = isset($expenses_data[$date]) ? $expenses_data[$date] : 0;
                    $profit = $sales - $expenses;
                    
                    fputcsv($output, [
                        $date,
                        $sales,
                        $expenses,
                        $profit
                    ]);
                }
                break;
        }
        
        // Đóng output stream
        fclose($output);
        exit;
        break;
    
    default:
        // Hiển thị trang tổng quan báo cáo
        include 'views/reports/index.php';
        break;
}
