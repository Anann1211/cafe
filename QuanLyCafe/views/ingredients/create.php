<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Thêm nguyên liệu mới</h1>
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
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="index.php?page=ingredients&action=create">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên nguyên liệu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="unit" class="form-label">Đơn vị tính <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit" name="unit" value="<?php echo isset($_POST['unit']) ? $_POST['unit'] : ''; ?>" required>
                            <small class="text-muted">Ví dụ: kg, g, lít, ml, gói, hộp, ...</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label">Số lượng tồn kho</label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?php echo isset($_POST['stock_quantity']) ? $_POST['stock_quantity'] : '0'; ?>" min="0" step="0.01">
                        </div>
                        
                        <div class="mb-3">
                            <label for="price_per_unit" class="form-label">Giá/Đơn vị <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price_per_unit" name="price_per_unit" value="<?php echo isset($_POST['price_per_unit']) ? $_POST['price_per_unit'] : ''; ?>" min="0" step="1000" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                            <select class="form-select" id="supplier_id" name="supplier_id">
                                <option value="">-- Chọn nhà cung cấp --</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo $supplier['id']; ?>" <?php echo (isset($_POST['supplier_id']) && $_POST['supplier_id'] == $supplier['id']) ? 'selected' : ''; ?>>
                                        <?php echo $supplier['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu nguyên liệu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
