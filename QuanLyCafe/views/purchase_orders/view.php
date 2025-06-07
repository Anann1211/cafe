<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chi tiết đơn nhập hàng</h1>
        <div>
            <button class="btn btn-info me-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i> In đơn nhập hàng
            </button>
            <a href="index.php?page=purchase-orders" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đơn nhập hàng</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">Mã đơn nhập hàng</th>
                                <td><?php echo $purchase_order->order_number; ?></td>
                            </tr>
                            <tr>
                                <th>Nhà cung cấp</th>
                                <td>
                                    <?php if ($purchase_order->supplier_id): ?>
                                        <a href="index.php?page=suppliers&action=view&id=<?php echo $purchase_order->supplier_id; ?>">
                                            <?php echo $purchase_order->supplier_name; ?>
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày nhập</th>
                                <td><?php echo formatDate($purchase_order->order_date); ?></td>
                            </tr>
                            <tr>
                                <th>Tổng tiền</th>
                                <td><?php echo formatCurrency($purchase_order->total_amount); ?></td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    <?php if ($purchase_order->status == 'completed'): ?>
                                        <span class="badge bg-success">Hoàn thành</span>
                                    <?php elseif ($purchase_order->status == 'pending'): ?>
                                        <span class="badge bg-warning">Đang xử lý</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Đã hủy</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Nhân viên</th>
                                <td><?php echo $purchase_order->user_name; ?></td>
                            </tr>
                            <tr>
                                <th>Ghi chú</th>
                                <td><?php echo $purchase_order->notes ? nl2br($purchase_order->notes) : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td><?php echo formatDate($purchase_order->created_at); ?></td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối</th>
                                <td><?php echo formatDate($purchase_order->updated_at); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Chi tiết nguyên liệu</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Nguyên liệu</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($order_items)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($order_items as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td>
                                                <a href="index.php?page=ingredients&action=view&id=<?php echo $item['ingredient_id']; ?>">
                                                    <?php echo $item['ingredient_name']; ?>
                                                </a>
                                            </td>
                                            <td><?php echo formatCurrency($item['unit_price']); ?></td>
                                            <td><?php echo $item['quantity'] . ' ' . $item['unit']; ?></td>
                                            <td><?php echo formatCurrency($item['total_price']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Tổng cộng:</th>
                                    <th><?php echo formatCurrency($purchase_order->total_amount); ?></th>
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
