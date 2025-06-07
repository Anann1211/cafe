<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Báo cáo tồn kho</h1>
        <div>
            <a href="index.php?page=reports&action=export&type=inventory" class="btn btn-success me-2">
                <i class="fas fa-file-excel me-1"></i> Xuất Excel
            </a>
            <a href="index.php?page=reports" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Tổng giá trị tồn kho nguyên liệu</h6>
                            <h3 class="mb-0"><?php echo formatCurrency($total_inventory_value); ?></h3>
                        </div>
                        <div class="bg-primary rounded p-3 text-white">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Tổng giá trị tồn kho sản phẩm</h6>
                            <h3 class="mb-0"><?php echo formatCurrency($total_product_value); ?></h3>
                        </div>
                        <div class="bg-success rounded p-3 text-white">
                            <i class="fas fa-coffee fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Tồn kho nguyên liệu</h5>
        </div>
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
                            <th>Giá trị</th>
                            <th>Nhà cung cấp</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ingredients)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ingredients as $ingredient): ?>
                                <tr>
                                    <td><?php echo $ingredient['id']; ?></td>
                                    <td><?php echo $ingredient['name']; ?></td>
                                    <td><?php echo $ingredient['unit']; ?></td>
                                    <td>
                                        <?php if ($ingredient['stock_quantity'] <= 10): ?>
                                            <span class="badge bg-danger"><?php echo $ingredient['stock_quantity']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $ingredient['stock_quantity']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo formatCurrency($ingredient['price_per_unit']); ?></td>
                                    <td><?php echo formatCurrency($ingredient['stock_quantity'] * $ingredient['price_per_unit']); ?></td>
                                    <td><?php echo $ingredient['supplier_name'] ? $ingredient['supplier_name'] : 'N/A'; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?page=ingredients&action=view&id=<?php echo $ingredient['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?page=ingredients&action=update-stock&id=<?php echo $ingredient['id']; ?>" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Cập nhật tồn kho">
                                                <i class="fas fa-edit"></i>
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
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Tồn kho sản phẩm</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Tồn kho</th>
                            <th>Giá bán</th>
                            <th>Giá trị</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo $product['code']; ?></td>
                                    <td><?php echo $product['name']; ?></td>
                                    <td>
                                        <?php
                                        $category_query = "SELECT name FROM categories WHERE id = ?";
                                        $stmt = $conn->prepare($category_query);
                                        $stmt->bind_param("i", $product['category_id']);
                                        $stmt->execute();
                                        $category_result = $stmt->get_result();
                                        
                                        if ($category_result->num_rows > 0) {
                                            echo $category_result->fetch_assoc()['name'];
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($product['stock_quantity'] <= 5): ?>
                                            <span class="badge bg-danger"><?php echo $product['stock_quantity']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $product['stock_quantity']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo formatCurrency($product['price']); ?></td>
                                    <td><?php echo formatCurrency($product['stock_quantity'] * $product['price']); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?page=products&action=view&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?page=products&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
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
