<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chi tiết hóa đơn</h1>
        <div>
            <a href="index.php?page=invoices&action=print&id=<?php echo $sales_order->id; ?>" class="btn btn-success me-2" target="_blank">
                <i class="fas fa-print me-1"></i> In hóa đơn
            </a>
            <button class="btn btn-info me-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i> In trang này
            </button>
            <a href="index.php?page=invoices" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông tin hóa đơn</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">Mã hóa đơn</th>
                                <td><?php echo $sales_order->order_number; ?></td>
                            </tr>
                            <tr>
                                <th>Ngày bán</th>
                                <td><?php echo formatDate($sales_order->order_date); ?></td>
                            </tr>
                            <tr>
                                <th>Tổng tiền</th>
                                <td><?php echo formatCurrency($sales_order->total_amount); ?></td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    <?php if ($sales_order->status == 'completed'): ?>
                                        <span class="badge bg-success">Hoàn thành</span>
                                    <?php elseif ($sales_order->status == 'pending'): ?>
                                        <span class="badge bg-warning">Đang xử lý</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Đã hủy</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Phương thức thanh toán</th>
                                <td>
                                    <?php
                                    switch ($sales_order->payment_method) {
                                        case 'cash':
                                            echo '<span class="badge bg-info">Tiền mặt</span>';
                                            break;
                                        case 'card':
                                            echo '<span class="badge bg-primary">Thẻ</span>';
                                            break;
                                        case 'transfer':
                                            echo '<span class="badge bg-secondary">Chuyển khoản</span>';
                                            break;
                                        default:
                                            echo $sales_order->payment_method;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Nhân viên</th>
                                <td><?php echo $sales_order->user_name; ?></td>
                            </tr>
                            <tr>
                                <th>Ghi chú</th>
                                <td><?php echo $sales_order->notes ? nl2br($sales_order->notes) : 'N/A'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <?php if ($customer_data): ?>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width: 40%;">Tên khách hàng</th>
                                    <td>
                                        <a href="index.php?page=customers&action=view&id=<?php echo $customer_data['id']; ?>">
                                            <?php echo $customer_data['name']; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại</th>
                                    <td><?php echo $customer_data['phone']; ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo $customer_data['email'] ? $customer_data['email'] : 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <th>Địa chỉ</th>
                                    <td><?php echo $customer_data['address'] ? $customer_data['address'] : 'N/A'; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="mb-0">Khách lẻ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Chi tiết sản phẩm</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã sản phẩm</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($order_items)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($order_items as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo $item['product_code']; ?></td>
                                            <td><?php echo $item['product_name']; ?></td>
                                            <td><?php echo formatCurrency($item['unit_price']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo formatCurrency($item['total_price']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">Tổng cộng:</th>
                                    <th><?php echo formatCurrency($sales_order->total_amount); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .navbar, .sidebar, .btn, footer {
            display: none !important;
        }
        
        .content {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>
