<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Báo cáo doanh thu</h1>
        <div>
            <a href="index.php?page=reports&action=export&type=sales&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" class="btn btn-success me-2">
                <i class="fas fa-file-excel me-1"></i> Xuất Excel
            </a>
            <a href="index.php?page=reports" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Tìm kiếm</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="page" value="reports">
                <input type="hidden" name="action" value="sales">
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="group_by" class="form-label">Nhóm theo</label>
                    <select class="form-select" id="group_by" name="group_by">
                        <option value="day" <?php echo ($group_by == 'day') ? 'selected' : ''; ?>>Ngày</option>
                        <option value="month" <?php echo ($group_by == 'month') ? 'selected' : ''; ?>>Tháng</option>
                        <option value="year" <?php echo ($group_by == 'year') ? 'selected' : ''; ?>>Năm</option>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Doanh thu từ <?php echo formatDate($date_from); ?> đến <?php echo formatDate($date_to); ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Số đơn hàng</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sales_data)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($sales_data as $data): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                switch ($group_by) {
                                                    case 'day':
                                                        echo formatDate($data['date']);
                                                        break;
                                                    case 'month':
                                                        echo date('m/Y', strtotime($data['date'] . '-01'));
                                                        break;
                                                    case 'year':
                                                        echo $data['date'];
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $data['order_count']; ?></td>
                                            <td><?php echo formatCurrency($data['total_sales']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Tổng cộng</th>
                                    <th><?php echo $total_orders; ?></th>
                                    <th><?php echo formatCurrency($total_sales); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top 5 sản phẩm bán chạy</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($top_products)): ?>
                        <p class="text-center">Không có dữ liệu</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_products as $product): ?>
                                        <tr>
                                            <td>
                                                <a href="index.php?page=products&action=view&id=<?php echo $product['id']; ?>">
                                                    <?php echo $product['name']; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $product['total_quantity']; ?></td>
                                            <td><?php echo formatCurrency($product['total_sales']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            <canvas id="productsChart"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dữ liệu cho biểu đồ doanh thu
        <?php if (!empty($sales_data)): ?>
        var salesData = {
            labels: [
                <?php
                foreach ($sales_data as $data) {
                    switch ($group_by) {
                        case 'day':
                            echo "'" . date('d/m/Y', strtotime($data['date'])) . "', ";
                            break;
                        case 'month':
                            echo "'" . date('m/Y', strtotime($data['date'] . '-01')) . "', ";
                            break;
                        case 'year':
                            echo "'" . $data['date'] . "', ";
                            break;
                    }
                }
                ?>
            ],
            datasets: [{
                label: 'Doanh thu',
                data: [<?php echo implode(', ', array_column($sales_data, 'total_sales')); ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };
        
        var salesCtx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: salesData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        <?php endif; ?>
        
        // Dữ liệu cho biểu đồ sản phẩm
        <?php if (!empty($top_products)): ?>
        var productsData = {
            labels: [<?php echo "'" . implode("', '", array_column($top_products, 'name')) . "'"; ?>],
            datasets: [{
                label: 'Số lượng bán',
                data: [<?php echo implode(', ', array_column($top_products, 'total_quantity')); ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };
        
        var productsCtx = document.getElementById('productsChart').getContext('2d');
        var productsChart = new Chart(productsCtx, {
            type: 'pie',
            data: productsData,
            options: {
                responsive: true
            }
        });
        <?php endif; ?>
    });
</script>

<?php include 'includes/footer.php'; ?>
