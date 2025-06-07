<?php
/**
 * Supplier Controller
 * 
 * Quản lý nhà cung cấp
 */

// Kiểm tra quyền admin
checkAdminAccess();

// Kết nối database
$conn = require_once 'config/database.php';

// Include Supplier model
require_once 'models/Supplier.php';

// Khởi tạo đối tượng Supplier
$supplier = new Supplier($conn);

// Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Xử lý các action
switch ($action) {
    case 'create':
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $supplier->name = sanitize($_POST['name']);
            $supplier->contact_person = sanitize($_POST['contact_person']);
            $supplier->email = sanitize($_POST['email']);
            $supplier->phone = sanitize($_POST['phone']);
            $supplier->address = sanitize($_POST['address']);
            
            // Validate dữ liệu
            $errors = [];
            
            if (empty($supplier->name)) {
                $errors[] = 'Tên nhà cung cấp không được để trống';
            }
            
            if (!empty($supplier->email) && !filter_var($supplier->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            }
            
            if (empty($supplier->phone)) {
                $errors[] = 'Số điện thoại không được để trống';
            }
            
            // Nếu không có lỗi, tạo nhà cung cấp mới
            if (empty($errors)) {
                if ($supplier->create()) {
                    setFlashMessage('Thêm nhà cung cấp thành công', 'success');
                    redirect('suppliers');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi thêm nhà cung cấp';
                }
            }
        }
        
        // Hiển thị form tạo nhà cung cấp
        include 'views/suppliers/create.php';
        break;
    
    case 'edit':
        // Lấy ID nhà cung cấp từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra nhà cung cấp tồn tại
        if (!$supplier->getById($id)) {
            setFlashMessage('Nhà cung cấp không tồn tại', 'danger');
            redirect('suppliers');
        }
        
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $supplier->name = sanitize($_POST['name']);
            $supplier->contact_person = sanitize($_POST['contact_person']);
            $supplier->email = sanitize($_POST['email']);
            $supplier->phone = sanitize($_POST['phone']);
            $supplier->address = sanitize($_POST['address']);
            
            // Validate dữ liệu
            $errors = [];
            
            if (empty($supplier->name)) {
                $errors[] = 'Tên nhà cung cấp không được để trống';
            }
            
            if (!empty($supplier->email) && !filter_var($supplier->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            }
            
            if (empty($supplier->phone)) {
                $errors[] = 'Số điện thoại không được để trống';
            }
            
            // Nếu không có lỗi, cập nhật nhà cung cấp
            if (empty($errors)) {
                if ($supplier->update()) {
                    setFlashMessage('Cập nhật nhà cung cấp thành công', 'success');
                    redirect('suppliers');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi cập nhật nhà cung cấp';
                }
            }
        }
        
        // Hiển thị form chỉnh sửa nhà cung cấp
        include 'views/suppliers/edit.php';
        break;
    
    case 'delete':
        // Lấy ID nhà cung cấp từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra nhà cung cấp tồn tại
        if (!$supplier->getById($id)) {
            setFlashMessage('Nhà cung cấp không tồn tại', 'danger');
            redirect('suppliers');
        }
        
        // Kiểm tra xem nhà cung cấp có đang được sử dụng không
        $ingredients = $supplier->getIngredients();
        $purchase_orders = $supplier->getPurchaseOrders();
        
        if (!empty($ingredients) || !empty($purchase_orders)) {
            setFlashMessage('Không thể xóa nhà cung cấp này vì đang được sử dụng', 'danger');
            redirect('suppliers');
        }
        
        // Xóa nhà cung cấp
        if ($supplier->delete()) {
            setFlashMessage('Xóa nhà cung cấp thành công', 'success');
        } else {
            setFlashMessage('Đã xảy ra lỗi khi xóa nhà cung cấp', 'danger');
        }
        
        redirect('suppliers');
        break;
    
    case 'view':
        // Lấy ID nhà cung cấp từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra nhà cung cấp tồn tại
        if (!$supplier->getById($id)) {
            setFlashMessage('Nhà cung cấp không tồn tại', 'danger');
            redirect('suppliers');
        }
        
        // Lấy danh sách nguyên liệu của nhà cung cấp
        $ingredients = $supplier->getIngredients();
        
        // Lấy lịch sử đơn hàng của nhà cung cấp
        $purchase_orders = $supplier->getPurchaseOrders();
        
        // Hiển thị thông tin nhà cung cấp
        include 'views/suppliers/view.php';
        break;
    
    default:
        // Lấy danh sách nhà cung cấp
        $suppliers = $supplier->getAll();
        
        // Hiển thị danh sách nhà cung cấp
        include 'views/suppliers/index.php';
        break;
}
