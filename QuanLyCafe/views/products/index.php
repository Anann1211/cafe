<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Quản lý sản phẩm</h1>
        <a href="index.php?page=products&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Thêm sản phẩm
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Loại</th>
                            <th>Kích cỡ</th>
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $item): ?>
                                <tr>
                                    <td><?php echo $item['code']; ?></td>
                                    <td>
                                        <?php if (!empty($item['image']) && file_exists('assets/images/products/' . $item['image'])): ?>
                                            <img src="assets/images/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-thumbnail" width="50">
                                        <?php else: ?>
                                            <img src="assets/images/no-image.jpg" alt="No Image" class="img-thumbnail" width="50">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $item['name']; ?></td>
                                    <td>
                                        <?php
                                        switch ($item['type']) {
                                            case 'phin':
                                                echo 'Phin';
                                                break;
                                            case 'machine':
                                                echo 'Máy';
                                                break;
                                            case 'instant':
                                                echo 'Hòa tan';
                                                break;
                                            default:
                                                echo $item['type'];
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        switch ($item['size']) {
                                            case 'small':
                                                echo 'Nhỏ';
                                                break;
                                            case 'medium':
                                                echo 'Vừa';
                                                break;
                                            case 'large':
                                                echo 'Lớn';
                                                break;
                                            default:
                                                echo $item['size'];
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo formatCurrency($item['price']); ?></td>
                                    <td>
                                        <?php if ($item['stock_quantity'] <= 10): ?>
                                            <span class="badge bg-danger"><?php echo $item['stock_quantity']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $item['stock_quantity']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?page=products&action=view&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?page=products&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?page=products&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Xóa">
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
