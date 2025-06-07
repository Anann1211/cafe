<?php
/**
 * Invoice Controller
 * 
 * Quản lý hóa đơn bán hàng
 */

// Kết nối database
$conn = require_once 'config/database.php';

// Include các model cần thiết
require_once 'models/SalesOrder.php';
require_once 'models/Customer.php';
require_once 'models/User.php';
require_once 'includes/pdf_generator.php';

// Khởi tạo đối tượng
$sales_order = new SalesOrder($conn);
$customer = new Customer($conn);
$user = new User($conn);

// Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Xử lý các action
switch ($action) {
    case 'view':
        // Lấy ID đơn hàng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra đơn hàng tồn tại
        if (!$sales_order->getById($id)) {
            setFlashMessage('Hóa đơn không tồn tại', 'danger');
            redirect('invoices');
        }
        
        // Lấy danh sách sản phẩm trong đơn hàng
        $order_items = $sales_order->getItems();
        
        // Lấy thông tin khách hàng
        $customer_data = null;
        if ($sales_order->customer_id) {
            $customer->getById($sales_order->customer_id);
            $customer_data = [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address
            ];
        }
        
        // Hiển thị chi tiết hóa đơn
        include 'views/invoices/view.php';
        break;
    
    case 'print':
        // Lấy ID đơn hàng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra đơn hàng tồn tại
        if (!$sales_order->getById($id)) {
            setFlashMessage('Hóa đơn không tồn tại', 'danger');
            redirect('invoices');
        }
        
        // Lấy danh sách sản phẩm trong đơn hàng
        $order_items = $sales_order->getItems();
        
        // Lấy thông tin khách hàng
        $customer_data = null;
        if ($sales_order->customer_id) {
            $customer->getById($sales_order->customer_id);
            $customer_data = [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address
            ];
        }
        
        // Chuẩn bị dữ liệu đơn hàng
        $order_data = [
            'id' => $sales_order->id,
            'order_number' => $sales_order->order_number,
            'customer_id' => $sales_order->customer_id,
            'user_id' => $sales_order->user_id,
            'user_name' => isset($sales_order->user_name) ? $sales_order->user_name : 'N/A',
            'order_date' => $sales_order->order_date,
            'total_amount' => $sales_order->total_amount,
            'status' => $sales_order->status,
            'payment_method' => $sales_order->payment_method,
            'notes' => $sales_order->notes
        ];
        
        // Tạo hóa đơn HTML
        $invoice_html = PDFGenerator::generateInvoice($order_data, $order_items, $customer_data);
        
        // Xuất hóa đơn HTML
        header('Content-Type: text/html; charset=utf-8');
        echo $invoice_html;
        exit;
        break;
    
    case 'search':
        // Lấy từ khóa tìm kiếm
        $keyword = isset($_GET['keyword']) ? sanitize($_GET['keyword']) : '';
        $date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
        $date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';
        $status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
        
        // Tìm kiếm hóa đơn
        $query = "SELECT so.*, c.name as customer_name, u.name as user_name 
                  FROM sales_orders so 
                  LEFT JOIN customers c ON so.customer_id = c.id 
                  LEFT JOIN users u ON so.user_id = u.id 
                  WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if (!empty($keyword)) {
            $query .= " AND (so.order_number LIKE ? OR c.name LIKE ? OR c.phone LIKE ?)";
            $keyword = "%{$keyword}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
            $types .= "sss";
        }
        
        if (!empty($date_from)) {
            $query .= " AND so.order_date >= ?";
            $params[] = $date_from;
            $types .= "s";
        }
        
        if (!empty($date_to)) {
            $query .= " AND so.order_date <= ?";
            $params[] = $date_to;
            $types .= "s";
        }
        
        if (!empty($status)) {
            $query .= " AND so.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        $query .= " ORDER BY so.order_date DESC";
        
        $stmt = $conn->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $invoices = $result->fetch_all(MYSQLI_ASSOC);
        
        // Hiển thị kết quả tìm kiếm
        include 'views/invoices/index.php';
        break;
    
    default:
        // Lấy danh sách hóa đơn
        $query = "SELECT so.*, c.name as customer_name, u.name as user_name 
                  FROM sales_orders so 
                  LEFT JOIN customers c ON so.customer_id = c.id 
                  LEFT JOIN users u ON so.user_id = u.id 
                  ORDER BY so.order_date DESC";
        
        $result = $conn->query($query);
        $invoices = $result->fetch_all(MYSQLI_ASSOC);
        
        // Hiển thị danh sách hóa đơn
        include 'views/invoices/index.php';
        break;
}
