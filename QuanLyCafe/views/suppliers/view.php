<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chi tiết nhà cung cấp</h1>
        <div>
            <a href="index.php?page=purchase-orders&action=create&supplier_id=<?php echo $supplier->id; ?>" class="btn btn-success me-2">
                <i class="fas fa-plus me-1"></i> Tạo đơn nhập hàng
            </a>
            <a href="index.php?page=suppliers&action=edit&id=<?php echo $supplier->id; ?>" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Chỉnh sửa
            </a>
            <a href="index.php?page=suppliers" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin nhà cung cấp</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">ID</th>
                                <td><?php echo $supplier->id; ?></td>
                            </tr>
                            <tr>
                                <th>Tên nhà cung cấp</th>
                                <td><?php echo $supplier->name; ?></td>
                            </tr>
                            <tr>
                                <th>Người liên hệ</th>
                                <td><?php echo $supplier->contact_person ? $supplier->contact_person : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo $supplier->email ? $supplier->email : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td><?php echo $supplier->phone; ?></td>
                            </tr>
                            <tr>
                                <th>Địa chỉ</th>
                                <td><?php echo $supplier->address ? $supplier->address : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td><?php echo formatDate($supplier->created_at); ?></td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối</th>
                                <td><?php echo formatDate($supplier->updated_at); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Nguyên liệu cung cấp</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên nguyên liệu</th>
                                    <th>Đơn vị</th>
                                    <th>Tồn kho</th>
                                    <th>Giá/Đơn vị</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ingredients)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($ingredients as $item): ?>
                                        <tr>
                                            <td><?php echo $item['id']; ?></td>
                                            <td><?php echo $item['name']; ?></td>
                                            <td><?php echo $item['unit']; ?></td>
                                            <td>
                                                <?php if ($item['stock_quantity'] <= 10): ?>
                                                    <span class="badge bg-danger"><?php echo $item['stock_quantity']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?php echo $item['stock_quantity']; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo formatCurrency($item['price_per_unit']); ?></td>
                                            <td>
                                                <a href="index.php?page=ingredients&action=view&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">
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
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lịch sử nhập hàng</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
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
                                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($purchase_orders as $order): ?>
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
                                            <td>
                                                <?php
                                                $user_query = "SELECT name FROM users WHERE id = ?";
                                                $stmt = $conn->prepare($user_query);
                                                $stmt->bind_param("i", $order['user_id']);
                                                $stmt->execute();
                                                $user_result = $stmt->get_result();
                                                
                                                if ($user_result->num_rows > 0) {
                                                    echo $user_result->fetch_assoc()['name'];
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="index.php?page=purchase-orders&action=view&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
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
