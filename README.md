# VietAn Coffee Shop Management System

Hệ thống quản lý bán hàng cafe VietAn là một ứng dụng web được phát triển bằng PHP và MySQL, giúp quản lý hoạt động kinh doanh của quán cafe một cách hiệu quả.

## Tính năng chính

### Quản lý

- Đăng nhập vào hệ thống
- Quản lý sản phẩm cà phê
- Quản lý nguyên liệu
- Quản lý nhà cung cấp
- Quản lý khách hàng
- Quản lý nhân viên
- Quản lý hóa đơn nhập
- Quản lý hóa đơn bán
- Xuất biểu mẫu và báo cáo
- Thống kê doanh thu, chi phí, tồn kho

### Nhân viên

- Đăng nhập hệ thống
- Bán hàng cho khách
- Tra cứu sản phẩm, khách hàng, hóa đơn
- Nhập nguyên liệu
- Tạo hóa đơn nhập và hóa đơn bán

## Yêu cầu hệ thống

- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Web server (Apache, Nginx)

## Cài đặt

1. Clone repository về máy local:

```
git clone https://github.com/yourusername/vietan-coffee.git
```

2. Cấu hình cơ sở dữ liệu:

- Mở file `config/database.php` và cập nhật thông tin kết nối cơ sở dữ liệu:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vietan_cafe');
```

3. Tạo cơ sở dữ liệu và các bảng:

- Truy cập URL: `http://localhost/config/schema.php`

4. Truy cập hệ thống:

- URL: `http://localhost/`

## Thông tin đăng nhập mặc định

- Email: admin@gmail.com
- Mật khẩu: admin123

## Cấu trúc thư mục

```
vietan-coffee/
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── fonts/
├── config/
│   ├── database.php
│   └── schema.php
├── controllers/
│   ├── AuthController.php
│   ├── CustomerController.php
│   ├── DashboardController.php
│   ├── HomeController.php
│   ├── IngredientController.php
│   ├── ProductController.php
│   ├── PurchaseOrderController.php
│   ├── ReportController.php
│   ├── SalesOrderController.php
│   ├── SupplierController.php
│   └── UserController.php
├── includes/
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   └── sidebar.php
├── models/
│   ├── Customer.php
│   ├── Ingredient.php
│   ├── Product.php
│   ├── PurchaseOrder.php
│   ├── Report.php
│   ├── SalesOrder.php
│   ├── Supplier.php
│   └── User.php
├── views/
│   ├── auth/
│   ├── customers/
│   ├── dashboard/
│   ├── ingredients/
│   ├── products/
│   ├── purchase_orders/
│   ├── reports/
│   ├── sales_orders/
│   ├── suppliers/
│   ├── templates/
│   └── users/
├── index.php
└── README.md
```

## Hướng dẫn sử dụng

### Đăng nhập

1. Truy cập URL: `http://localhost/vietan-coffee`
2. Nhập email và mật khẩu
3. Nhấn nút "Đăng nhập"

### Quản lý sản phẩm

1. Đăng nhập với tài khoản quản lý
2. Chọn "Sản phẩm" từ menu bên trái
3. Thêm, sửa, xóa sản phẩm

### Bán hàng

1. Đăng nhập với tài khoản nhân viên hoặc quản lý
2. Chọn "Bán hàng" từ menu bên trái
3. Thêm sản phẩm vào giỏ hàng
4. Nhập thông tin khách hàng (nếu có)
5. Hoàn tất đơn hàng

## Liên hệ

Nếu bạn có bất kỳ câu hỏi hoặc góp ý nào, vui lòng liên hệ:

- Email: admin@gmail.com
- Điện thoại: 0123456789
