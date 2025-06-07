<?php
/**
 * Purchase Order Controller
 * 
 * Quản lý nhập hàng
 */

// Kiểm tra quyền admin
checkAdminAccess();

// Kết nối database
$conn = require_once 'config/database.php';

// Include các model cần thiết
require_once 'models/PurchaseOrder.php';
require_once 'models/Supplier.php';
require_once 'models/Ingredient.php';

// Khởi tạo đối tượng
$purchase_order = new PurchaseOrder($conn);
$supplier = new Supplier($conn);
$ingredient = new Ingredient($conn);

// Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Khởi tạo giỏ hàng nhập nếu chưa có
if (!isset($_SESSION['purchase_cart'])) {
    $_SESSION['purchase_cart'] = [
        'items' => [],
        'total' => 0,
        'supplier_id' => null
    ];
}

// Xử lý các action
switch ($action) {
    case 'create':
        // Lấy supplier_id từ URL nếu có
        if (isset($_GET['supplier_id']) && !empty($_GET['supplier_id'])) {
            $_SESSION['purchase_cart']['supplier_id'] = (int)$_GET['supplier_id'];
            
            // Lấy thông tin nhà cung cấp
            $supplier->getById($_SESSION['purchase_cart']['supplier_id']);
        }
        
        // Lấy danh sách nhà cung cấp
        $suppliers = $supplier->getAll();
        
        // Lấy danh sách nguyên liệu
        $ingredients_query = "SELECT i.*, s.name as supplier_name 
                             FROM ingredients i 
                             LEFT JOIN suppliers s ON i.supplier_id = s.id 
                             ORDER BY i.name ASC";
        $ingredients_result = $conn->query($ingredients_query);
        $ingredients = $ingredients_result->fetch_all(MYSQLI_ASSOC);
        
        // Hiển thị form tạo đơn nhập hàng
        include 'views/purchase_orders/create.php';
        break;
    
    case 'add-item':
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $ingredient_id = (int)$_POST['ingredient_id'];
            $quantity = (float)$_POST['quantity'];
            $unit_price = (float)$_POST['unit_price'];
            
            // Validate dữ liệu
            $errors = [];
            
            if ($ingredient_id <= 0) {
                $errors[] = 'Vui lòng chọn nguyên liệu';
            }
            
            if ($quantity <= 0) {
                $errors[] = 'Số lượng phải lớn hơn 0';
            }
            
            if ($unit_price <= 0) {
                $errors[] = 'Đơn giá phải lớn hơn 0';
            }
            
            // Nếu không có lỗi, thêm vào giỏ hàng nhập
            if (empty($errors)) {
                // Lấy thông tin nguyên liệu
                $ingredient->getById($ingredient_id);
                
                // Kiểm tra nguyên liệu đã có trong giỏ hàng chưa
                $item_index = -1;
                foreach ($_SESSION['purchase_cart']['items'] as $index => $item) {
                    if ($item['id'] == $ingredient_id) {
                        $item_index = $index;
                        break;
                    }
                }
                
                // Nếu nguyên liệu đã có trong giỏ hàng, cập nhật số lượng
                if ($item_index >= 0) {
                    $_SESSION['purchase_cart']['items'][$item_index]['quantity'] += $quantity;
                    $_SESSION['purchase_cart']['items'][$item_index]['unit_price'] = $unit_price;
                    $_SESSION['purchase_cart']['items'][$item_index]['total'] = $_SESSION['purchase_cart']['items'][$item_index]['quantity'] * $unit_price;
                } else {
                    // Thêm nguyên liệu mới vào giỏ hàng
                    $_SESSION['purchase_cart']['items'][] = [
                        'id' => $ingredient_id,
                        'name' => $ingredient->name,
                        'unit' => $ingredient->unit,
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'total' => $quantity * $unit_price
                    ];
                }
                
                // Cập nhật tổng tiền
                $_SESSION['purchase_cart']['total'] = 0;
                foreach ($_SESSION['purchase_cart']['items'] as $item) {
                    $_SESSION['purchase_cart']['total'] += $item['total'];
                }
                
                setFlashMessage('Thêm nguyên liệu vào đơn nhập hàng thành công', 'success');
            } else {
                setFlashMessage($errors[0], 'danger');
            }
        }
        
        redirect('purchase-orders', ['action' => 'create']);
        break;
    
    case 'remove-item':
        // Lấy ID nguyên liệu từ URL
        $ingredient_id = isset($_GET['ingredient_id']) ? (int)$_GET['ingredient_id'] : 0;
        
        // Xóa nguyên liệu khỏi giỏ hàng nhập
        foreach ($_SESSION['purchase_cart']['items'] as $index => $item) {
            if ($item['id'] == $ingredient_id) {
                unset($_SESSION['purchase_cart']['items'][$index]);
                break;
            }
        }
        
        // Sắp xếp lại mảng
        $_SESSION['purchase_cart']['items'] = array_values($_SESSION['purchase_cart']['items']);
        
        // Cập nhật tổng tiền
        $_SESSION['purchase_cart']['total'] = 0;
        foreach ($_SESSION['purchase_cart']['items'] as $item) {
            $_SESSION['purchase_cart']['total'] += $item['total'];
        }
        
        setFlashMessage('Xóa nguyên liệu khỏi đơn nhập hàng thành công', 'success');
        redirect('purchase-orders', ['action' => 'create']);
        break;
    
    case 'clear-cart':
        // Xóa giỏ hàng nhập
        $_SESSION['purchase_cart'] = [
            'items' => [],
            'total' => 0,
            'supplier_id' => null
        ];
        
        setFlashMessage('Đã xóa đơn nhập hàng', 'success');
        redirect('purchase-orders', ['action' => 'create']);
        break;
    
    case 'checkout':
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $supplier_id = (int)$_POST['supplier_id'];
            $notes = sanitize($_POST['notes']);
            
            // Validate dữ liệu
            $errors = [];
            
            if ($supplier_id <= 0) {
                $errors[] = 'Vui lòng chọn nhà cung cấp';
            }
            
            if (empty($_SESSION['purchase_cart']['items'])) {
                $errors[] = 'Vui lòng thêm ít nhất một nguyên liệu vào đơn nhập hàng';
            }
            
            // Nếu không có lỗi, tạo đơn nhập hàng
            if (empty($errors)) {
                // Tạo đơn nhập hàng mới
                $purchase_order->order_number = $purchase_order->generateOrderNumber();
                $purchase_order->supplier_id = $supplier_id;
                $purchase_order->user_id = getCurrentUserId();
                $purchase_order->order_date = date('Y-m-d');
                $purchase_order->total_amount = $_SESSION['purchase_cart']['total'];
                $purchase_order->status = 'completed';
                $purchase_order->notes = $notes;
                
                // Lưu đơn nhập hàng
                if ($purchase_order->create()) {
                    // Thêm các nguyên liệu vào đơn nhập hàng
                    foreach ($_SESSION['purchase_cart']['items'] as $item) {
                        $purchase_order->addItem($item['id'], $item['quantity'], $item['unit_price']);
                        
                        // Cập nhật số lượng tồn kho
                        $purchase_order->updateIngredientStock($item['id'], $item['quantity']);
                    }
                    
                    // Xóa giỏ hàng nhập
                    $_SESSION['purchase_cart'] = [
                        'items' => [],
                        'total' => 0,
                        'supplier_id' => null
                    ];
                    
                    setFlashMessage('Tạo đơn nhập hàng thành công', 'success');
                    redirect('purchase-orders', ['action' => 'view', 'id' => $purchase_order->id]);
                } else {
                    setFlashMessage('Đã xảy ra lỗi khi tạo đơn nhập hàng', 'danger');
                    redirect('purchase-orders', ['action' => 'create']);
                }
            } else {
                setFlashMessage($errors[0], 'danger');
                redirect('purchase-orders', ['action' => 'create']);
            }
        } else {
            redirect('purchase-orders', ['action' => 'create']);
        }
        break;
    
    case 'view':
        // Lấy ID đơn nhập hàng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra đơn nhập hàng tồn tại
        if (!$purchase_order->getById($id)) {
            setFlashMessage('Đơn nhập hàng không tồn tại', 'danger');
            redirect('purchase-orders');
        }
        
        // Lấy danh sách nguyên liệu trong đơn nhập hàng
        $order_items = $purchase_order->getItems();
        
        // Hiển thị chi tiết đơn nhập hàng
        include 'views/purchase_orders/view.php';
        break;
    
    case 'delete':
        // Lấy ID đơn nhập hàng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra đơn nhập hàng tồn tại
        if (!$purchase_order->getById($id)) {
            setFlashMessage('Đơn nhập hàng không tồn tại', 'danger');
            redirect('purchase-orders');
        }
        
        // Kiểm tra trạng thái đơn nhập hàng
        if ($purchase_order->status == 'completed') {
            setFlashMessage('Không thể xóa đơn nhập hàng đã hoàn thành', 'danger');
            redirect('purchase-orders');
        }
        
        // Xóa đơn nhập hàng
        if ($purchase_order->delete()) {
            setFlashMessage('Xóa đơn nhập hàng thành công', 'success');
        } else {
            setFlashMessage('Đã xảy ra lỗi khi xóa đơn nhập hàng', 'danger');
        }
        
        redirect('purchase-orders');
        break;
    
    default:
        // Lấy danh sách đơn nhập hàng
        $purchase_orders = $purchase_order->getAll();
        
        // Hiển thị danh sách đơn nhập hàng
        include 'views/purchase_orders/index.php';
        break;
}
