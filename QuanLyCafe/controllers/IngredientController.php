<?php
/**
 * Ingredient Controller
 * 
 * Quản lý nguyên liệu
 */

// Kiểm tra quyền admin
checkAdminAccess();

// Kết nối database
$conn = require_once 'config/database.php';

// Include các model cần thiết
require_once 'models/Ingredient.php';
require_once 'models/Supplier.php';

// Khởi tạo đối tượng
$ingredient = new Ingredient($conn);
$supplier = new Supplier($conn);

// Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Xử lý các action
switch ($action) {
    case 'create':
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $ingredient->name = sanitize($_POST['name']);
            $ingredient->unit = sanitize($_POST['unit']);
            $ingredient->stock_quantity = (float)$_POST['stock_quantity'];
            $ingredient->price_per_unit = (float)$_POST['price_per_unit'];
            $ingredient->supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
            
            // Validate dữ liệu
            $errors = [];
            
            if (empty($ingredient->name)) {
                $errors[] = 'Tên nguyên liệu không được để trống';
            }
            
            if (empty($ingredient->unit)) {
                $errors[] = 'Đơn vị tính không được để trống';
            }
            
            if ($ingredient->price_per_unit <= 0) {
                $errors[] = 'Giá nguyên liệu phải lớn hơn 0';
            }
            
            // Nếu không có lỗi, tạo nguyên liệu mới
            if (empty($errors)) {
                if ($ingredient->create()) {
                    setFlashMessage('Thêm nguyên liệu thành công', 'success');
                    redirect('ingredients');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi thêm nguyên liệu';
                }
            }
        }
        
        // Lấy danh sách nhà cung cấp
        $suppliers = $supplier->getAll();
        
        // Hiển thị form tạo nguyên liệu
        include 'views/ingredients/create.php';
        break;
    
    case 'edit':
        // Lấy ID nguyên liệu từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra nguyên liệu tồn tại
        if (!$ingredient->getById($id)) {
            setFlashMessage('Nguyên liệu không tồn tại', 'danger');
            redirect('ingredients');
        }
        
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $ingredient->name = sanitize($_POST['name']);
            $ingredient->unit = sanitize($_POST['unit']);
            $ingredient->stock_quantity = (float)$_POST['stock_quantity'];
            $ingredient->price_per_unit = (float)$_POST['price_per_unit'];
            $ingredient->supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
            
            // Validate dữ liệu
            $errors = [];
            
            if (empty($ingredient->name)) {
                $errors[] = 'Tên nguyên liệu không được để trống';
            }
            
            if (empty($ingredient->unit)) {
                $errors[] = 'Đơn vị tính không được để trống';
            }
            
            if ($ingredient->price_per_unit <= 0) {
                $errors[] = 'Giá nguyên liệu phải lớn hơn 0';
            }
            
            // Nếu không có lỗi, cập nhật nguyên liệu
            if (empty($errors)) {
                if ($ingredient->update()) {
                    setFlashMessage('Cập nhật nguyên liệu thành công', 'success');
                    redirect('ingredients');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi cập nhật nguyên liệu';
                }
            }
        }
        
        // Lấy danh sách nhà cung cấp
        $suppliers = $supplier->getAll();
        
        // Hiển thị form chỉnh sửa nguyên liệu
        include 'views/ingredients/edit.php';
        break;
    
    case 'delete':
        // Lấy ID nguyên liệu từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra nguyên liệu tồn tại
        if (!$ingredient->getById($id)) {
            setFlashMessage('Nguyên liệu không tồn tại', 'danger');
            redirect('ingredients');
        }
        
        // Xóa nguyên liệu
        if ($ingredient->delete()) {
            setFlashMessage('Xóa nguyên liệu thành công', 'success');
        } else {
            setFlashMessage('Đã xảy ra lỗi khi xóa nguyên liệu', 'danger');
        }
        
        redirect('ingredients');
        break;
    
    case 'view':
        // Lấy ID nguyên liệu từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra nguyên liệu tồn tại
        if (!$ingredient->getById($id)) {
            setFlashMessage('Nguyên liệu không tồn tại', 'danger');
            redirect('ingredients');
        }
        
        // Hiển thị thông tin nguyên liệu
        include 'views/ingredients/view.php';
        break;
    
    case 'update-stock':
        // Lấy ID nguyên liệu từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra nguyên liệu tồn tại
        if (!$ingredient->getById($id)) {
            setFlashMessage('Nguyên liệu không tồn tại', 'danger');
            redirect('ingredients');
        }
        
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $quantity = (float)$_POST['quantity'];
            $type = sanitize($_POST['type']);
            
            // Validate dữ liệu
            $errors = [];
            
            if ($quantity <= 0) {
                $errors[] = 'Số lượng phải lớn hơn 0';
            }
            
            if ($type == 'subtract' && $quantity > $ingredient->stock_quantity) {
                $errors[] = 'Số lượng giảm không được lớn hơn số lượng tồn kho';
            }
            
            // Nếu không có lỗi, cập nhật số lượng tồn kho
            if (empty($errors)) {
                // Nếu là giảm số lượng, đổi dấu
                if ($type == 'subtract') {
                    $quantity = -$quantity;
                }
                
                if ($ingredient->updateStock($quantity)) {
                    setFlashMessage('Cập nhật số lượng tồn kho thành công', 'success');
                    redirect('ingredients');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi cập nhật số lượng tồn kho';
                }
            }
        }
        
        // Hiển thị form cập nhật số lượng tồn kho
        include 'views/ingredients/update_stock.php';
        break;
    
    default:
        // Lấy danh sách nguyên liệu
        $ingredients = $ingredient->getAll();
        
        // Hiển thị danh sách nguyên liệu
        include 'views/ingredients/index.php';
        break;
}
