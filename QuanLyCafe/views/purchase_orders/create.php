<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Tạo đơn nhập hàng</h1>
        <div>
            <a href="index.php?page=purchase-orders&action=clear-cart" class="btn btn-warning me-2" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn nhập hàng này?')">
                <i class="fas fa-trash me-1"></i> Xóa đơn
            </a>
            <a href="index.php?page=purchase-orders" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thêm nguyên liệu vào đơn</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=purchase-orders&action=add-item" class="row g-3">
                        <div class="col-md-5">
                            <label for="ingredient_id" class="form-label">Nguyên liệu <span class="text-danger">*</span></label>
                            <select class="form-select" id="ingredient_id" name="ingredient_id" required>
                                <option value="">-- Chọn nguyên liệu --</option>
                                <?php foreach ($ingredients as $item): ?>
                                    <option value="<?php echo $item['id']; ?>" data-unit="<?php echo $item['unit']; ?>" data-price="<?php echo $item['price_per_unit']; ?>">
                                        <?php echo $item['name']; ?> (<?php echo $item['unit']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantity" name="quantity" min="0.01" step="0.01" value="1" required>
                                <span class="input-group-text" id="unit-display">Đơn vị</span>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="unit_price" class="form-label">Đơn giá <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="unit_price" name="unit_price" min="0" step="1000" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="table-responsive mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Nguyên liệu</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <?php if (empty($_SESSION['purchase_cart']['items'])): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Chưa có nguyên liệu nào trong đơn</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($_SESSION['purchase_cart']['items'] as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo $item['name']; ?></td>
                                            <td><?php echo formatCurrency($item['unit_price']); ?></td>
                                            <td><?php echo $item['quantity'] . ' ' . $item['unit']; ?></td>
                                            <td><?php echo formatCurrency($item['total']); ?></td>
                                            <td>
                                                <a href="index.php?page=purchase-orders&action=remove-item&ingredient_id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa nguyên liệu này?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Tổng cộng:</th>
                                    <th id="cart-total"><?php echo formatCurrency($_SESSION['purchase_cart']['total']); ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đơn nhập hàng</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=purchase-orders&action=checkout">
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                            <select class="form-select" id="supplier_id" name="supplier_id" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                <?php foreach ($suppliers as $supplier_item): ?>
                                    <option value="<?php echo $supplier_item['id']; ?>" <?php echo ($_SESSION['purchase_cart']['supplier_id'] == $supplier_item['id']) ? 'selected' : ''; ?>>
                                        <?php echo $supplier_item['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success" <?php echo empty($_SESSION['purchase_cart']['items']) ? 'disabled' : ''; ?>>
                                <i class="fas fa-check me-1"></i> Hoàn tất đơn nhập hàng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Cập nhật đơn vị và giá khi chọn nguyên liệu
        $('#ingredient_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var unit = selectedOption.data('unit');
            var price = selectedOption.data('price');
            
            $('#unit-display').text(unit);
            $('#unit_price').val(price);
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
