<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chi tiết nguyên liệu</h1>
        <div>
            <a href="index.php?page=ingredients&action=update-stock&id=<?php echo $ingredient->id; ?>" class="btn btn-success me-2">
                <i class="fas fa-boxes me-1"></i> Cập nhật tồn kho
            </a>
            <a href="index.php?page=ingredients&action=edit&id=<?php echo $ingredient->id; ?>" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Chỉnh sửa
            </a>
            <a href="index.php?page=ingredients" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin nguyên liệu</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">ID</th>
                                <td><?php echo $ingredient->id; ?></td>
                            </tr>
                            <tr>
                                <th>Tên nguyên liệu</th>
                                <td><?php echo $ingredient->name; ?></td>
                            </tr>
                            <tr>
                                <th>Đơn vị tính</th>
                                <td><?php echo $ingredient->unit; ?></td>
                            </tr>
                            <tr>
                                <th>Số lượng tồn kho</th>
                                <td>
                                    <?php if ($ingredient->stock_quantity <= 10): ?>
                                        <span class="badge bg-danger"><?php echo $ingredient->stock_quantity; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo $ingredient->stock_quantity; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Giá/Đơn vị</th>
                                <td><?php echo formatCurrency($ingredient->price_per_unit); ?></td>
                            </tr>
                            <tr>
                                <th>Nhà cung cấp</th>
                                <td>
                                    <?php if ($ingredient->supplier_id): ?>
                                        <a href="index.php?page=suppliers&action=view&id=<?php echo $ingredient->supplier_id; ?>">
                                            <?php echo $ingredient->supplier_name; ?>
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td><?php echo formatDate($ingredient->created_at); ?></td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối</th>
                                <td><?php echo formatDate($ingredient->updated_at); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lịch sử nhập hàng</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Lấy lịch sử nhập hàng của nguyên liệu
                    $query = "
                        SELECT po.id, po.order_number, po.order_date, poi.quantity, poi.unit_price, s.name as supplier_name
                        FROM purchase_order_items poi
                        JOIN purchase_orders po ON poi.purchase_order_id = po.id
                        LEFT JOIN suppliers s ON po.supplier_id = s.id
                        WHERE poi.ingredient_id = ? AND po.status = 'completed'
                        ORDER BY po.order_date DESC
                        LIMIT 10
                    ";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $ingredient->id);
                    $stmt->execute();
                    $purchase_history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày nhập</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Nhà cung cấp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($purchase_history)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Chưa có lịch sử nhập hàng</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($purchase_history as $item): ?>
                                        <tr>
                                            <td>
                                                <a href="index.php?page=purchase-orders&action=view&id=<?php echo $item['id']; ?>">
                                                    <?php echo $item['order_number']; ?>
                                                </a>
                                            </td>
                                            <td><?php echo formatDate($item['order_date']); ?></td>
                                            <td><?php echo $item['quantity'] . ' ' . $ingredient->unit; ?></td>
                                            <td><?php echo formatCurrency($item['unit_price']); ?></td>
                                            <td><?php echo $item['supplier_name']; ?></td>
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
