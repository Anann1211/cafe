<?php
/**
 * User Controller
 *
 * Quản lý người dùng (nhân viên)
 */

// Kết nối database
$conn = require_once 'config/database.php';

// Include User model
require_once 'models/User.php';

// Tạo đối tượng User
$user = new User($conn);

// Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Xử lý các action
switch ($action) {
    case 'create':
        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $user->name = sanitize($_POST['name']);
            $user->email = sanitize($_POST['email']);
            $user->password = $_POST['password'];
            $user->role = sanitize($_POST['role']);
            $user->phone = sanitize($_POST['phone']);
            $user->address = sanitize($_POST['address']);
            $user->gender = isset($_POST['gender']) ? sanitize($_POST['gender']) : null;
            $user->birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
            $user->hire_date = !empty($_POST['hire_date']) ? $_POST['hire_date'] : date('Y-m-d');
            $user->status = sanitize($_POST['status']);

            // Validate dữ liệu
            $errors = [];

            if (empty($user->name)) {
                $errors[] = 'Họ tên không được để trống';
            }

            if (empty($user->email)) {
                $errors[] = 'Email không được để trống';
            } elseif (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            } elseif ($user->emailExists($user->email)) {
                $errors[] = 'Email đã tồn tại trong hệ thống';
            }

            if (empty($user->password)) {
                $errors[] = 'Mật khẩu không được để trống';
            } elseif (strlen($user->password) < 6) {
                $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            if (empty($user->role)) {
                $errors[] = 'Vai trò không được để trống';
            }

            // Nếu không có lỗi, tạo người dùng mới
            if (empty($errors)) {
                if ($user->create()) {
                    setFlashMessage('Thêm người dùng thành công', 'success');
                    redirect('users');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi thêm người dùng';
                }
            }
        }

        // Hiển thị form tạo người dùng
        include 'views/users/create.php';
        break;

    case 'edit':
        // Lấy ID người dùng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Kiểm tra người dùng tồn tại
        if (!$user->getById($id)) {
            setFlashMessage('Người dùng không tồn tại', 'danger');
            redirect('users');
        }

        // Kiểm tra form submit
        if (isPostRequest()) {
            // Lấy dữ liệu từ form
            $user->name = sanitize($_POST['name']);
            $user->email = sanitize($_POST['email']);
            $user->role = sanitize($_POST['role']);
            $user->phone = sanitize($_POST['phone']);
            $user->address = sanitize($_POST['address']);
            $user->gender = isset($_POST['gender']) ? sanitize($_POST['gender']) : null;
            $user->birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
            $user->hire_date = !empty($_POST['hire_date']) ? $_POST['hire_date'] : null;
            $user->status = sanitize($_POST['status']);

            // Validate dữ liệu
            $errors = [];

            if (empty($user->name)) {
                $errors[] = 'Họ tên không được để trống';
            }

            if (empty($user->email)) {
                $errors[] = 'Email không được để trống';
            } elseif (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            } elseif ($user->emailExists($user->email, $user->id)) {
                $errors[] = 'Email đã tồn tại trong hệ thống';
            }

            if (empty($user->role)) {
                $errors[] = 'Vai trò không được để trống';
            }

            // Nếu không có lỗi, cập nhật người dùng
            if (empty($errors)) {
                if ($user->update()) {
                    // Kiểm tra nếu có mật khẩu mới
                    if (!empty($_POST['password'])) {
                        if (strlen($_POST['password']) < 6) {
                            setFlashMessage('Mật khẩu phải có ít nhất 6 ký tự', 'danger');
                        } else {
                            $user->updatePassword($_POST['password']);
                            setFlashMessage('Cập nhật người dùng và mật khẩu thành công', 'success');
                        }
                    } else {
                        setFlashMessage('Cập nhật người dùng thành công', 'success');
                    }

                    redirect('users');
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi cập nhật người dùng';
                }
            }
        }

        // Hiển thị form chỉnh sửa người dùng
        include 'views/users/edit.php';
        break;

    case 'delete':
        // Lấy ID người dùng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Kiểm tra người dùng tồn tại
        if (!$user->getById($id)) {
            setFlashMessage('Người dùng không tồn tại', 'danger');
            redirect('users');
        }

        // Không cho phép xóa chính mình
        if ($id == getCurrentUserId()) {
            setFlashMessage('Bạn không thể xóa tài khoản của chính mình', 'danger');
            redirect('users');
        }

        // Xóa người dùng
        if ($user->delete()) {
            setFlashMessage('Xóa người dùng thành công', 'success');
        } else {
            setFlashMessage('Đã xảy ra lỗi khi xóa người dùng', 'danger');
        }

        redirect('users');
        break;

    case 'view':
        // Lấy ID người dùng từ URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // Kiểm tra người dùng tồn tại
        if (!$user->getById($id)) {
            setFlashMessage('Người dùng không tồn tại', 'danger');
            redirect('users');
        }

        // Hiển thị thông tin người dùng
        include 'views/users/view.php';
        break;

    default:
        // Lấy danh sách người dùng
        $users = $user->getAll();

        // Hiển thị danh sách người dùng
        include 'views/users/index.php';
        break;
}
