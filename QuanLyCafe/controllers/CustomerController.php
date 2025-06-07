<?php
/**
 * Customer Controller
 * 
 * Quản lý khách hàng
 */

// Kết nối database
$conn = require_once 'config/database.php';

// Include Customer model
require_once 'models/Customer.php';

// Khởi tạo đối tượng Customer
$customer = new Customer($conn);

// Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Xử lý các action
switch ($action) {
    case 'create':
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $customer->name = sanitize($_POST['name']);
            $customer->email = sanitize($_POST['email']);
            $customer->phone = sanitize($_POST['phone']);
            $customer->address = sanitize($_POST['address']);
            
            // Validate dữ liệu
            $errors = [];
            
            if (empty($customer->name)) {
                $errors[] = 'Tên khách hàng không được để trống';
            }
            
            if (!empty($customer->email) && !filter_var($customer->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            }
            
            if (empty($customer->phone)) {
                $errors[] = 'Số điện thoại không được để trống';
            }
            
            // Nếu không có lỗi, tạo khách hàng mới
            if (empty($errors)) {
                if ($customer->create()) {
                    setFlashMessage('Thêm khách hàng thành công', 'success');
                    redirect('customers');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi thêm khách hàng';
                }
            }
        }
        
        // Hiển thị form tạo khách hàng
        include 'views/customers/create.php';
        break;
    
    case 'edit':
        // Lấy ID khách hàng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra khách hàng tồn tại
        if (!$customer->getById($id)) {
            setFlashMessage('Khách hàng không tồn tại', 'danger');
            redirect('customers');
        }
        
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $customer->name = sanitize($_POST['name']);
            $customer->email = sanitize($_POST['email']);
            $customer->phone = sanitize($_POST['phone']);
            $customer->address = sanitize($_POST['address']);
            
            // Validate dữ liệu
            $errors = [];
            
            if (empty($customer->name)) {
                $errors[] = 'Tên khách hàng không được để trống';
            }
            
            if (!empty($customer->email) && !filter_var($customer->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            }
            
            if (empty($customer->phone)) {
                $errors[] = 'Số điện thoại không được để trống';
            }
            
            // Nếu không có lỗi, cập nhật khách hàng
            if (empty($errors)) {
                if ($customer->update()) {
                    setFlashMessage('Cập nhật khách hàng thành công', 'success');
                    redirect('customers');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi cập nhật khách hàng';
                }
            }
        }
        
        // Hiển thị form chỉnh sửa khách hàng
        include 'views/customers/edit.php';
        break;
    
    case 'delete':
        // Lấy ID khách hàng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra khách hàng tồn tại
        if (!$customer->getById($id)) {
            setFlashMessage('Khách hàng không tồn tại', 'danger');
            redirect('customers');
        }
        
        // Xóa khách hàng
        if ($customer->delete()) {
            setFlashMessage('Xóa khách hàng thành công', 'success');
        } else {
            setFlashMessage('Đã xảy ra lỗi khi xóa khách hàng', 'danger');
        }
        
        redirect('customers');
        break;
    
    case 'view':
        // Lấy ID khách hàng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra khách hàng tồn tại
        if (!$customer->getById($id)) {
            setFlashMessage('Khách hàng không tồn tại', 'danger');
            redirect('customers');
        }
        
        // Lấy lịch sử mua hàng của khách hàng
        $purchase_history = $customer->getPurchaseHistory();
        
        // Hiển thị thông tin khách hàng
        include 'views/customers/view.php';
        break;
    
    default:
        // Lấy danh sách khách hàng
        $customers = $customer->getAll();
        
        // Hiển thị danh sách khách hàng
        include 'views/customers/index.php';
        break;
}
