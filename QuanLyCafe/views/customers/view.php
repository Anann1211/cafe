<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chi tiết khách hàng</h1>
        <div>
            <a href="index.php?page=customers&action=edit&id=<?php echo $customer->id; ?>" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Chỉnh sửa
            </a>
            <a href="index.php?page=customers" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">ID</th>
                                <td><?php echo $customer->id; ?></td>
                            </tr>
                            <tr>
                                <th>Tên khách hàng</th>
                                <td><?php echo $customer->name; ?></td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td><?php echo $customer->phone; ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo $customer->email ? $customer->email : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Địa chỉ</th>
                                <td><?php echo $customer->address ? $customer->address : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td><?php echo formatDate($customer->created_at); ?></td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối</th>
                                <td><?php echo formatDate($customer->updated_at); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lịch sử mua hàng</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Ngày mua</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Nhân viên bán hàng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($purchase_history)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Khách hàng chưa có đơn hàng nào</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($purchase_history as $order): ?>
                                        <tr>
                                            <td><?php echo $order['order_number']; ?></td>
                                            <td><?php echo formatDate($order['order_date']); ?></td>
                                            <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                            <td>
                                                <?php if ($order['status'] == 'completed'): ?>
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                <?php elseif ($order['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning">Đang xử lý</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $order['user_name']; ?></td>
                                            <td>
                                                <a href="index.php?page=sales-orders&action=view&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
