<?php
/**
 * Sales Order Controller
 *
 * Xử lý chức năng bán hàng
 */

// Kết nối database
$conn = require_once 'config/database.php';

// Include các model cần thiết
require_once 'models/SalesOrder.php';
require_once 'models/Product.php';
require_once 'models/Customer.php';
require_once 'includes/pdf_generator.php';

// Khởi tạo đối tượng
$sales_order = new SalesOrder($conn);
$product = new Product($conn);
$customer = new Customer($conn);

// Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        'items' => [],
        'total' => 0
    ];
}

// Xử lý các action
switch ($action) {
    case 'add-to-cart':
        // Xử lý AJAX request
        if (isPostRequest() && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            // Kiểm tra sản phẩm tồn tại
            if ($product->getById($product_id)) {
                // Kiểm tra số lượng tồn kho
                if ($product->stock_quantity < $quantity) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Số lượng sản phẩm trong kho không đủ'
                    ]);
                    exit;
                }

                // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
                $item_index = -1;
                foreach ($_SESSION['cart']['items'] as $index => $item) {
                    if ($item['id'] == $product_id) {
                        $item_index = $index;
                        break;
                    }
                }

                // Nếu sản phẩm đã có trong giỏ hàng, cập nhật số lượng
                if ($item_index >= 0) {
                    $_SESSION['cart']['items'][$item_index]['quantity'] += $quantity;
                    $_SESSION['cart']['items'][$item_index]['total'] = $_SESSION['cart']['items'][$item_index]['quantity'] * $_SESSION['cart']['items'][$item_index]['price'];
                } else {
                    // Thêm sản phẩm mới vào giỏ hàng
                    $_SESSION['cart']['items'][] = [
                        'id' => $product_id,
                        'name' => $product->name,
                        'code' => $product->code,
                        'price' => $product->price,
                        'quantity' => $quantity,
                        'total' => $product->price * $quantity
                    ];
                }

                // Cập nhật tổng tiền
                $_SESSION['cart']['total'] = 0;
                foreach ($_SESSION['cart']['items'] as $item) {
                    $_SESSION['cart']['total'] += $item['total'];
                }

                echo json_encode([
                    'success' => true,
                    'cart' => $_SESSION['cart']
                ]);
                exit;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại'
                ]);
                exit;
            }
        }

        // Nếu không phải AJAX request, chuyển hướng về trang bán hàng
        redirect('sales-orders');
        break;

    case 'update-cart':
        // Xử lý AJAX request
        if (isPostRequest() && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            // Kiểm tra số lượng hợp lệ
            if ($quantity <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Số lượng phải lớn hơn 0'
                ]);
                exit;
            }

            // Kiểm tra sản phẩm tồn tại
            if ($product->getById($product_id)) {
                // Kiểm tra số lượng tồn kho
                if ($product->stock_quantity < $quantity) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Số lượng sản phẩm trong kho không đủ'
                    ]);
                    exit;
                }

                // Cập nhật số lượng trong giỏ hàng
                foreach ($_SESSION['cart']['items'] as $index => $item) {
                    if ($item['id'] == $product_id) {
                        $_SESSION['cart']['items'][$index]['quantity'] = $quantity;
                        $_SESSION['cart']['items'][$index]['total'] = $quantity * $item['price'];
                        break;
                    }
                }

                // Cập nhật tổng tiền
                $_SESSION['cart']['total'] = 0;
                foreach ($_SESSION['cart']['items'] as $item) {
                    $_SESSION['cart']['total'] += $item['total'];
                }

                echo json_encode([
                    'success' => true,
                    'cart' => $_SESSION['cart']
                ]);
                exit;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại'
                ]);
                exit;
            }
        }

        // Nếu không phải AJAX request, chuyển hướng về trang bán hàng
        redirect('sales-orders');
        break;

    case 'remove-from-cart':
        // Xử lý AJAX request
        if (isPostRequest() && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

            // Xóa sản phẩm khỏi giỏ hàng
            foreach ($_SESSION['cart']['items'] as $index => $item) {
                if ($item['id'] == $product_id) {
                    unset($_SESSION['cart']['items'][$index]);
                    break;
                }
            }

            // Sắp xếp lại mảng
            $_SESSION['cart']['items'] = array_values($_SESSION['cart']['items']);

            // Cập nhật tổng tiền
            $_SESSION['cart']['total'] = 0;
            foreach ($_SESSION['cart']['items'] as $item) {
                $_SESSION['cart']['total'] += $item['total'];
            }

            echo json_encode([
                'success' => true,
                'cart' => $_SESSION['cart']
            ]);
            exit;
        }

        // Nếu không phải AJAX request, chuyển hướng về trang bán hàng
        redirect('sales-orders');
        break;

    case 'clear-cart':
        // Xóa giỏ hàng
        $_SESSION['cart'] = [
            'items' => [],
            'total' => 0
        ];

        setFlashMessage('Giỏ hàng đã được xóa', 'success');
        redirect('sales-orders');
        break;

    case 'checkout':
        // Kiểm tra giỏ hàng có sản phẩm không
        if (empty($_SESSION['cart']['items'])) {
            setFlashMessage('Giỏ hàng trống, vui lòng thêm sản phẩm vào giỏ hàng', 'danger');
            redirect('sales-orders');
        }

        // Xử lý form thanh toán
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $customer_id = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
            $payment_method = sanitize($_POST['payment_method']);
            $notes = sanitize($_POST['notes']);

            // Tạo đơn hàng mới
            $sales_order->order_number = $sales_order->generateOrderNumber();
            $sales_order->customer_id = $customer_id;
            $sales_order->user_id = getCurrentUserId();
            $sales_order->order_date = date('Y-m-d');
            $sales_order->total_amount = $_SESSION['cart']['total'];
            $sales_order->status = 'completed';
            $sales_order->payment_method = $payment_method;
            $sales_order->notes = $notes;

            // Lưu đơn hàng
            if ($sales_order->create()) {
                // Thêm các sản phẩm vào đơn hàng
                foreach ($_SESSION['cart']['items'] as $item) {
                    $sales_order->addItem($item['id'], $item['quantity'], $item['price']);

                    // Cập nhật số lượng tồn kho
                    $sales_order->updateProductStock($item['id'], $item['quantity']);
                }

                // Xóa giỏ hàng
                $_SESSION['cart'] = [
                    'items' => [],
                    'total' => 0
                ];

                setFlashMessage('Đơn hàng đã được tạo thành công', 'success');
                redirect('sales-orders', ['action' => 'view', 'id' => $sales_order->id]);
            } else {
                setFlashMessage('Đã xảy ra lỗi khi tạo đơn hàng', 'danger');
            }
        }

        // Lấy danh sách khách hàng
        $customers = $customer->getAll();

        // Hiển thị form thanh toán
        include 'views/sales_orders/checkout.php';
        break;

    case 'view':
        // Lấy ID đơn hàng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Kiểm tra đơn hàng tồn tại
        if (!$sales_order->getById($id)) {
            setFlashMessage('Đơn hàng không tồn tại', 'danger');
            redirect('sales-orders');
        }

        // Lấy danh sách sản phẩm trong đơn hàng
        $order_items = $sales_order->getItems();

        // Hiển thị chi tiết đơn hàng
        include 'views/sales_orders/view.php';
        break;

    case 'print-invoice':
        // Lấy ID đơn hàng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Kiểm tra đơn hàng tồn tại
        if (!$sales_order->getById($id)) {
            setFlashMessage('Đơn hàng không tồn tại', 'danger');
            redirect('sales-orders');
        }

        // Lấy danh sách sản phẩm trong đơn hàng
        $order_items = $sales_order->getItems();

        // Lấy thông tin khách hàng nếu có
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

    default:
        // Lấy danh sách sản phẩm
        $products = $product->getAll();

        // Hiển thị trang bán hàng
        include 'views/sales_orders/index.php';
        break;
}
