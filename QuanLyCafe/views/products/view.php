<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chi tiết sản phẩm</h1>
        <div>
            <a href="index.php?page=products&action=edit&id=<?php echo $product->id; ?>" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Chỉnh sửa
            </a>
            <a href="index.php?page=products" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($product->image) && file_exists('assets/images/products/' . $product->image)): ?>
                        <img src="assets/images/products/<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>" class="img-fluid mb-3" style="max-height: 300px;">
                    <?php else: ?>
                        <img src="assets/images/no-image.jpg" alt="No Image" class="img-fluid mb-3" style="max-height: 300px;">
                    <?php endif; ?>
                    
                    <h4><?php echo $product->name; ?></h4>
                    <h5 class="text-primary"><?php echo formatCurrency($product->price); ?></h5>
                    
                    <div class="d-flex justify-content-center mt-3">
                        <span class="badge bg-info me-2">
                            <?php
                            switch ($product->type) {
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
                                    echo $product->type;
                            }
                            ?>
                        </span>
                        <span class="badge bg-secondary">
                            <?php
                            switch ($product->size) {
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
                                    echo $product->size;
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin sản phẩm</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Mã sản phẩm</th>
                                <td><?php echo $product->code; ?></td>
                            </tr>
                            <tr>
                                <th>Danh mục</th>
                                <td>
                                    <?php
                                    $category_query = "SELECT name FROM categories WHERE id = ?";
                                    $stmt = $conn->prepare($category_query);
                                    $stmt->bind_param("i", $product->category_id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if ($result->num_rows > 0) {
                                        echo $result->fetch_assoc()['name'];
                                    } else {
                                        echo 'Không có';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Loại cà phê</th>
                                <td>
                                    <?php
                                    switch ($product->type) {
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
                                            echo $product->type;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Kích cỡ</th>
                                <td>
                                    <?php
                                    switch ($product->size) {
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
                                            echo $product->size;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Giá bán</th>
                                <td><?php echo formatCurrency($product->price); ?></td>
                            </tr>
                            <tr>
                                <th>Số lượng tồn kho</th>
                                <td>
                                    <?php if ($product->stock_quantity <= 10): ?>
                                        <span class="badge bg-danger"><?php echo $product->stock_quantity; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo $product->stock_quantity; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td><?php echo formatDate($product->created_at); ?></td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối</th>
                                <td><?php echo formatDate($product->updated_at); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mô tả sản phẩm</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($product->description)): ?>
                        <p class="text-muted">Không có mô tả</p>
                    <?php else: ?>
                        <p><?php echo nl2br($product->description); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
