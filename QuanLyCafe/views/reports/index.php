<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Báo cáo & Thống kê</h1>
    </div>
    
    <div class="row">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-chart-line fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">Báo cáo doanh thu</h5>
                    <p class="card-text">Thống kê doanh thu theo ngày, tháng, năm và sản phẩm bán chạy.</p>
                    <a href="index.php?page=reports&action=sales" class="btn btn-primary">
                        <i class="fas fa-eye me-1"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-money-bill-wave fa-3x text-danger"></i>
                    </div>
                    <h5 class="card-title">Báo cáo chi phí</h5>
                    <p class="card-text">Thống kê chi phí nhập hàng theo ngày, tháng, năm.</p>
                    <a href="index.php?page=reports&action=expenses" class="btn btn-danger">
                        <i class="fas fa-eye me-1"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-boxes fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">Báo cáo tồn kho</h5>
                    <p class="card-text">Thống kê tồn kho nguyên liệu và sản phẩm.</p>
                    <a href="index.php?page=reports&action=inventory" class="btn btn-success">
                        <i class="fas fa-eye me-1"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-chart-pie fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">Báo cáo lợi nhuận</h5>
                    <p class="card-text">Thống kê lợi nhuận (doanh thu - chi phí) theo thời gian.</p>
                    <a href="index.php?page=reports&action=profit" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thống kê nhanh</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Thống kê doanh thu hôm nay
                    $today = date('Y-m-d');
                    $query = "SELECT SUM(total_amount) as total FROM sales_orders WHERE status = 'completed' AND DATE(order_date) = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $today);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $today_sales = $result->fetch_assoc()['total'] ?? 0;
                    
                    // Thống kê doanh thu tháng này
                    $month_start = date('Y-m-01');
                    $month_end = date('Y-m-t');
                    $query = "SELECT SUM(total_amount) as total FROM sales_orders WHERE status = 'completed' AND order_date BETWEEN ? AND ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $month_start, $month_end);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $month_sales = $result->fetch_assoc()['total'] ?? 0;
                    
                    // Thống kê chi phí tháng này
                    $query = "SELECT SUM(total_amount) as total FROM purchase_orders WHERE status = 'completed' AND order_date BETWEEN ? AND ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $month_start, $month_end);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $month_expenses = $result->fetch_assoc()['total'] ?? 0;
                    
                    // Tính lợi nhuận tháng này
                    $month_profit = $month_sales - $month_expenses;
                    ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Doanh thu hôm nay</h6>
                                    <h3 class="text-primary"><?php echo formatCurrency($today_sales); ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Doanh thu tháng này</h6>
                                    <h3 class="text-primary"><?php echo formatCurrency($month_sales); ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Chi phí tháng này</h6>
                                    <h3 class="text-danger"><?php echo formatCurrency($month_expenses); ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Lợi nhuận tháng này</h6>
                                    <h3 class="<?php echo $month_profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo formatCurrency($month_profit); ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cảnh báo tồn kho</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Lấy danh sách nguyên liệu sắp hết
                    $query = "SELECT * FROM ingredients WHERE stock_quantity <= 10 ORDER BY stock_quantity ASC LIMIT 5";
                    $result = $conn->query($query);
                    $low_stock_ingredients = $result->fetch_all(MYSQLI_ASSOC);
                    
                    // Lấy danh sách sản phẩm sắp hết
                    $query = "SELECT * FROM products WHERE stock_quantity <= 5 ORDER BY stock_quantity ASC LIMIT 5";
                    $result = $conn->query($query);
                    $low_stock_products = $result->fetch_all(MYSQLI_ASSOC);
                    ?>
                    
                    <h6>Nguyên liệu sắp hết</h6>
                    <?php if (empty($low_stock_ingredients)): ?>
                        <p class="text-success">Không có nguyên liệu nào sắp hết.</p>
                    <?php else: ?>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên nguyên liệu</th>
                                        <th>Tồn kho</th>
                                        <th>Đơn vị</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($low_stock_ingredients as $ingredient): ?>
                                        <tr>
                                            <td><?php echo $ingredient['name']; ?></td>
                                            <td>
                                                <span class="badge bg-danger"><?php echo $ingredient['stock_quantity']; ?></span>
                                            </td>
                                            <td><?php echo $ingredient['unit']; ?></td>
                                            <td>
                                                <a href="index.php?page=ingredients&action=update-stock&id=<?php echo $ingredient['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                    <h6>Sản phẩm sắp hết</h6>
                    <?php if (empty($low_stock_products)): ?>
                        <p class="text-success">Không có sản phẩm nào sắp hết.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên sản phẩm</th>
                                        <th>Tồn kho</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($low_stock_products as $product): ?>
                                        <tr>
                                            <td><?php echo $product['name']; ?></td>
                                            <td>
                                                <span class="badge bg-danger"><?php echo $product['stock_quantity']; ?></span>
                                            </td>
                                            <td>
                                                <a href="index.php?page=products&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
