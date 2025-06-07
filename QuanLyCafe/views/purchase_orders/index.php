<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Quản lý nhập hàng</h1>
        <a href="index.php?page=purchase-orders&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tạo đơn nhập hàng
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Nhà cung cấp</th>
                            <th>Ngày nhập</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Nhân viên</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchase_orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($purchase_orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_number']; ?></td>
                                    <td>
                                        <?php if ($order['supplier_id']): ?>
                                            <a href="index.php?page=suppliers&action=view&id=<?php echo $order['supplier_id']; ?>">
                                                <?php echo $order['supplier_name']; ?>
                                            </a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
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
                                        <div class="btn-group">
                                            <a href="index.php?page=purchase-orders&action=view&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($order['status'] != 'completed'): ?>
                                                <a href="index.php?page=purchase-orders&action=delete&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn nhập hàng này?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
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

<?php include 'includes/footer.php'; ?>
