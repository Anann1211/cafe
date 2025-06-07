<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Quản lý nguyên liệu</h1>
        <a href="index.php?page=ingredients&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Thêm nguyên liệu
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên nguyên liệu</th>
                            <th>Đơn vị</th>
                            <th>Tồn kho</th>
                            <th>Giá/Đơn vị</th>
                            <th>Nhà cung cấp</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ingredients)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có dữ liệu</td>
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
                                    <td><?php echo $item['supplier_name'] ? $item['supplier_name'] : 'N/A'; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?page=ingredients&action=view&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?page=ingredients&action=update-stock&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Cập nhật tồn kho">
                                                <i class="fas fa-boxes"></i>
                                            </a>
                                            <a href="index.php?page=ingredients&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?page=ingredients&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa nguyên liệu này?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
