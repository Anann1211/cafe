<?php
/**
 * Product Controller
 *
 * Handles product management functionality
 */

// Include database connection
$conn = require_once 'config/database.php';

// Include Product model
require_once 'models/Product.php';

// Create Product instance
$product = new Product($conn);

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle actions
switch ($action) {
    case 'create':
        // Check if form is submitted
        if (isPostRequest()) {
            // Get form data
            $product->code = sanitize($_POST['code']);
            $product->name = sanitize($_POST['name']);
            $product->category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $product->type = sanitize($_POST['type']);
            $product->size = sanitize($_POST['size']);
            $product->price = (float)$_POST['price'];
            $product->description = sanitize($_POST['description']);
            $product->stock_quantity = (int)$_POST['stock_quantity'];
            $product->image = '';

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = uploadFile($_FILES['image'], 'assets/images/products');

                if ($image) {
                    $product->image = $image;
                }
            }

            // Validate form data
            $errors = [];

            if (empty($product->code)) {
                $errors[] = 'Mã sản phẩm không được để trống';
            } elseif ($product->codeExists($product->code)) {
                $errors[] = 'Mã sản phẩm đã tồn tại';
            }

            if (empty($product->name)) {
                $errors[] = 'Tên sản phẩm không được để trống';
            }

            if (empty($product->type)) {
                $errors[] = 'Loại cà phê không được để trống';
            }

            if (empty($product->size)) {
                $errors[] = 'Kích cỡ không được để trống';
            }

            if ($product->price <= 0) {
                $errors[] = 'Giá bán phải lớn hơn 0';
            }

            // If no errors, create product
            if (empty($errors)) {
                if ($product->create()) {
                    setFlashMessage('Thêm sản phẩm thành công', 'success');
                    redirect('products');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi thêm sản phẩm';
                }
            }
        }

        // Get all categories
        $categories_query = "SELECT id, name FROM categories ORDER BY name ASC";
        $categories_result = $conn->query($categories_query);
        $categories = $categories_result->fetch_all(MYSQLI_ASSOC);

        // Include create view
        include 'views/products/create.php';
        break;

    case 'edit':
        // Get product ID from URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Check if product exists
        if (!$product->getById($id)) {
            setFlashMessage('Sản phẩm không tồn tại', 'danger');
            redirect('products');
        }

        // Check if form is submitted
        if (isPostRequest()) {
            // Get form data
            $product->code = sanitize($_POST['code']);
            $product->name = sanitize($_POST['name']);
            $product->category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            $product->type = sanitize($_POST['type']);
            $product->size = sanitize($_POST['size']);
            $product->price = (float)$_POST['price'];
            $product->description = sanitize($_POST['description']);
            $product->stock_quantity = (int)$_POST['stock_quantity'];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = uploadFile($_FILES['image'], 'assets/images/products');

                if ($image) {
                    // Delete old image if exists
                    if (!empty($product->image) && file_exists('assets/images/products/' . $product->image)) {
                        unlink('assets/images/products/' . $product->image);
                    }

                    $product->image = $image;
                }
            }

            // Validate form data
            $errors = [];

            if (empty($product->code)) {
                $errors[] = 'Mã sản phẩm không được để trống';
            } elseif ($product->codeExists($product->code, $product->id)) {
                $errors[] = 'Mã sản phẩm đã tồn tại';
            }

            if (empty($product->name)) {
                $errors[] = 'Tên sản phẩm không được để trống';
            }

            if (empty($product->type)) {
                $errors[] = 'Loại cà phê không được để trống';
            }

            if (empty($product->size)) {
                $errors[] = 'Kích cỡ không được để trống';
            }

            if ($product->price <= 0) {
                $errors[] = 'Giá bán phải lớn hơn 0';
            }

            // If no errors, update product
            if (empty($errors)) {
                if ($product->update()) {
                    setFlashMessage('Cập nhật sản phẩm thành công', 'success');
                    redirect('products');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi cập nhật sản phẩm';
                }
            }
        }

        // Get all categories
        $categories_query = "SELECT id, name FROM categories ORDER BY name ASC";
        $categories_result = $conn->query($categories_query);
        $categories = $categories_result->fetch_all(MYSQLI_ASSOC);

        // Include edit view
        include 'views/products/edit.php';
        break;

    case 'delete':
        // Get product ID from URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Check if product exists
        if (!$product->getById($id)) {
            setFlashMessage('Sản phẩm không tồn tại', 'danger');
            redirect('products');
        }

        // Delete product
        if ($product->delete()) {
            // Delete product image if exists
            if (!empty($product->image) && file_exists('assets/images/products/' . $product->image)) {
                unlink('assets/images/products/' . $product->image);
            }

            setFlashMessage('Xóa sản phẩm thành công', 'success');
        } else {
            setFlashMessage('Đã xảy ra lỗi khi xóa sản phẩm', 'danger');
        }

        redirect('products');
        break;

    case 'view':
        // Get product ID from URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Check if product exists
        if (!$product->getById($id)) {
            setFlashMessage('Sản phẩm không tồn tại', 'danger');
            redirect('products');
        }

        // Include view
        include 'views/products/view.php';
        break;

    default:
        // Get all products
        $products = $product->getAll();

        // Include index view
        include 'views/products/index.php';
        break;
}
