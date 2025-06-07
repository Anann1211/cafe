<?php
/**
 * Kiểm soát quyền truy cập
 * 
 * File này chứa các hàm kiểm tra quyền truy cập cho các trang khác nhau
 */

/**
 * Kiểm tra quyền truy cập cho các trang chỉ dành cho admin
 * Nếu người dùng không phải là admin, chuyển hướng về dashboard
 */
function checkAdminAccess() {
    if (!isAdmin()) {
        setFlashMessage('Bạn không có quyền truy cập trang này', 'danger');
        redirect('dashboard');
        exit;
    }
}

/**
 * Kiểm tra quyền truy cập cho các trang yêu cầu đăng nhập
 * Nếu người dùng chưa đăng nhập, chuyển hướng về trang đăng nhập
 */
function checkLoginAccess() {
    if (!isLoggedIn()) {
        redirect('login');
        exit;
    }
}

/**
 * Danh sách các trang chỉ dành cho admin
 */
$admin_only_pages = [
    'ingredients',
    'purchase-orders',
    'suppliers',
    'users',
    'reports'
];

/**
 * Kiểm tra quyền truy cập dựa trên trang hiện tại
 * 
 * @param string $page Tên trang cần kiểm tra
 */
function checkPageAccess($page) {
    global $admin_only_pages;
    
    // Kiểm tra đăng nhập cho tất cả các trang (trừ trang login)
    if ($page != 'login') {
        checkLoginAccess();
    }
    
    // Kiểm tra quyền admin cho các trang chỉ dành cho admin
    if (in_array($page, $admin_only_pages)) {
        checkAdminAccess();
    }
}
