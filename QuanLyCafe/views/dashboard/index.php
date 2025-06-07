<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Bảng điều khiển</h1>
        <div>
            <span class="text-muted"><?php echo date('d/m/Y'); ?></span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="card stats-card primary mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Doanh thu hôm nay</h6>
                            <h4 class="mb-0"><?php echo formatCurrency($today_sales); ?></h4>
                        </div>
                        <div class="icon text-primary">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card stats-card success mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Doanh thu tháng này</h6>
                            <h4 class="mb-0"><?php echo formatCurrency($month_sales); ?></h4>
                        </div>
                        <div class="icon text-success">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card stats-card warning mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Tổng sản phẩm</h6>
                            <h4 class="mb-0"><?php echo $total_products; ?></h4>
                        </div>
                        <div class="icon text-warning">
                            <i class="fas fa-coffee"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card stats-card danger mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Tổng khách hàng</h6>
                            <h4 class="mb-0"><?php echo $total_customers; ?></h4>
                        </div>
                        <div class="icon text-danger">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Sales -->
        <div class="<?php echo isAdmin() ? 'col-md-6' : 'col-md-12'; ?>">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Đơn hàng gần đây</h5>
                    <a href="index.php?page=sales-orders" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_sales)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_sales as $sale): ?>
                                        <tr>
                                            <td><a href="index.php?page=sales-orders&action=view&id=<?php echo $sale['id']; ?>"><?php echo $sale['order_number']; ?></a></td>
                                            <td><?php echo formatDate($sale['order_date']); ?></td>
                                            <td><?php echo $sale['customer_name'] ? $sale['customer_name'] : 'Khách lẻ'; ?></td>
                                            <td><?php echo formatCurrency($sale['total_amount']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isAdmin()): ?>
        <!-- Recent Purchases - Chỉ hiển thị cho Admin -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nhập hàng gần đây</h5>
                    <a href="index.php?page=purchase-orders" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_purchases)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_purchases as $purchase): ?>
                                        <tr>
                                            <td><a href="index.php?page=purchase-orders&action=view&id=<?php echo $purchase['id']; ?>"><?php echo $purchase['order_number']; ?></a></td>
                                            <td><?php echo formatDate($purchase['order_date']); ?></td>
                                            <td><?php echo $purchase['supplier_name']; ?></td>
                                            <td><?php echo formatCurrency($purchase['total_amount']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <!-- Low Stock Products -->
        <div class="<?php echo isAdmin() ? 'col-md-6' : 'col-md-12'; ?>">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sản phẩm sắp hết</h5>
                    <a href="index.php?page=products" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($low_stock_products)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($low_stock_products as $product): ?>
                                        <tr>
                                            <td><?php echo $product['code']; ?></td>
                                            <td><?php echo $product['name']; ?></td>
                                            <td><span class="badge bg-danger"><?php echo $product['stock_quantity']; ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isAdmin()): ?>
        <!-- Low Stock Ingredients - Chỉ hiển thị cho Admin -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nguyên liệu sắp hết</h5>
                    <a href="index.php?page=ingredients" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tên nguyên liệu</th>
                                    <th>Đơn vị</th>
                                    <th>Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($low_stock_ingredients)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($low_stock_ingredients as $ingredient): ?>
                                        <tr>
                                            <td><?php echo $ingredient['name']; ?></td>
                                            <td><?php echo $ingredient['unit']; ?></td>
                                            <td><span class="badge bg-danger"><?php echo $ingredient['stock_quantity']; ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
