<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Báo cáo chi phí</h1>
        <div>
            <a href="index.php?page=reports&action=export&type=expenses&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" class="btn btn-success me-2">
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
                <input type="hidden" name="action" value="expenses">
                
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
                    <h5 class="mb-0">Chi phí từ <?php echo formatDate($date_from); ?> đến <?php echo formatDate($date_to); ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Số đơn nhập hàng</th>
                                    <th>Chi phí</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($expenses_data)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($expenses_data as $data): ?>
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
                                            <td><?php echo formatCurrency($data['total_expenses']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Tổng cộng</th>
                                    <th><?php echo $total_orders; ?></th>
                                    <th><?php echo formatCurrency($total_expenses); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <canvas id="expensesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top 5 nguyên liệu nhập nhiều nhất</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($top_ingredients)): ?>
                        <p class="text-center">Không có dữ liệu</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nguyên liệu</th>
                                        <th>Số lượng</th>
                                        <th>Chi phí</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_ingredients as $ingredient): ?>
                                        <tr>
                                            <td>
                                                <a href="index.php?page=ingredients&action=view&id=<?php echo $ingredient['id']; ?>">
                                                    <?php echo $ingredient['name']; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $ingredient['total_quantity'] . ' ' . $ingredient['unit']; ?></td>
                                            <td><?php echo formatCurrency($ingredient['total_expenses']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            <canvas id="ingredientsChart"></canvas>
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
        // Dữ liệu cho biểu đồ chi phí
        <?php if (!empty($expenses_data)): ?>
        var expensesData = {
            labels: [
                <?php
                foreach ($expenses_data as $data) {
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
                label: 'Chi phí',
                data: [<?php echo implode(', ', array_column($expenses_data, 'total_expenses')); ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };
        
        var expensesCtx = document.getElementById('expensesChart').getContext('2d');
        var expensesChart = new Chart(expensesCtx, {
            type: 'bar',
            data: expensesData,
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
        
        // Dữ liệu cho biểu đồ nguyên liệu
        <?php if (!empty($top_ingredients)): ?>
        var ingredientsData = {
            labels: [<?php echo "'" . implode("', '", array_column($top_ingredients, 'name')) . "'"; ?>],
            datasets: [{
                label: 'Chi phí',
                data: [<?php echo implode(', ', array_column($top_ingredients, 'total_expenses')); ?>],
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
        
        var ingredientsCtx = document.getElementById('ingredientsChart').getContext('2d');
        var ingredientsChart = new Chart(ingredientsCtx, {
            type: 'pie',
            data: ingredientsData,
            options: {
                responsive: true
            }
        });
        <?php endif; ?>
    });
</script>

<?php include 'includes/footer.php'; ?>
