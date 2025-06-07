<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Cập nhật tồn kho nguyên liệu</h1>
        <a href="index.php?page=ingredients" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cập nhật số lượng tồn kho</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=ingredients&action=update-stock&id=<?php echo $ingredient->id; ?>">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : ''; ?>" min="0.01" step="0.01" required>
                                <span class="input-group-text"><?php echo $ingredient->unit; ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Loại cập nhật <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="add" <?php echo (isset($_POST['type']) && $_POST['type'] == 'add') ? 'selected' : ''; ?>>Thêm vào tồn kho</option>
                                <option value="subtract" <?php echo (isset($_POST['type']) && $_POST['type'] == 'subtract') ? 'selected' : ''; ?>>Giảm từ tồn kho</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="note" name="note" rows="3"><?php echo isset($_POST['note']) ? $_POST['note'] : ''; ?></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Cập nhật tồn kho
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
