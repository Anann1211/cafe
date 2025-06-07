<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Thêm sản phẩm mới</h1>
        <a href="index.php?page=products" class="btn btn-secondary">
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
            <form method="POST" action="index.php?page=products&action=create" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Mã sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" value="<?php echo isset($_POST['code']) ? $_POST['code'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Loại cà phê <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">-- Chọn loại --</option>
                                <option value="phin" <?php echo (isset($_POST['type']) && $_POST['type'] == 'phin') ? 'selected' : ''; ?>>Phin</option>
                                <option value="machine" <?php echo (isset($_POST['type']) && $_POST['type'] == 'machine') ? 'selected' : ''; ?>>Máy</option>
                                <option value="instant" <?php echo (isset($_POST['type']) && $_POST['type'] == 'instant') ? 'selected' : ''; ?>>Hòa tan</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="size" class="form-label">Kích cỡ <span class="text-danger">*</span></label>
                            <select class="form-select" id="size" name="size" required>
                                <option value="">-- Chọn kích cỡ --</option>
                                <option value="small" <?php echo (isset($_POST['size']) && $_POST['size'] == 'small') ? 'selected' : ''; ?>>Nhỏ</option>
                                <option value="medium" <?php echo (isset($_POST['size']) && $_POST['size'] == 'medium') ? 'selected' : ''; ?>>Vừa</option>
                                <option value="large" <?php echo (isset($_POST['size']) && $_POST['size'] == 'large') ? 'selected' : ''; ?>>Lớn</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá bán <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>" min="0" step="1000" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label">Số lượng tồn kho</label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?php echo isset($_POST['stock_quantity']) ? $_POST['stock_quantity'] : '0'; ?>" min="0">
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Chấp nhận các định dạng: JPG, JPEG, PNG, GIF. Kích thước tối đa: 2MB.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả sản phẩm</label>
                            <textarea class="form-control" id="description" name="description" rows="5"><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
