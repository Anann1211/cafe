<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Báo cáo lợi nhuận</h1>
        <div>
            <a href="index.php?page=reports&action=export&type=profit&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" class="btn btn-success me-2">
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
                <input type="hidden" name="action" value="profit">
                
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
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Tổng doanh thu</h6>
                            <h3 class="mb-0 text-primary"><?php echo formatCurrency($total_sales); ?></h3>
                        </div>
                        <div class="bg-primary rounded p-3 text-white">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Tổng chi phí</h6>
                            <h3 class="mb-0 text-danger"><?php echo formatCurrency($total_expenses); ?></h3>
                        </div>
                        <div class="bg-danger rounded p-3 text-white">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Tổng lợi nhuận</h6>
                            <h3 class="mb-0 <?php echo $total_profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo formatCurrency($total_profit); ?>
                            </h3>
                        </div>
                        <div class="bg-success rounded p-3 text-white">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Lợi nhuận từ <?php echo formatDate($date_from); ?> đến <?php echo formatDate($date_to); ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Doanh thu</th>
                            <th>Chi phí</th>
                            <th>Lợi nhuận</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($profit_data)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($profit_data as $date => $data): ?>
                                <tr>
                                    <td>
                                        <?php
                                        switch ($group_by) {
                                            case 'day':
                                                echo formatDate($date);
                                                break;
                                            case 'month':
                                                echo date('m/Y', strtotime($date . '-01'));
                                                break;
                                            case 'year':
                                                echo $date;
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo formatCurrency($data['sales']); ?></td>
                                    <td><?php echo formatCurrency($data['expenses']); ?></td>
                                    <td class="<?php echo $data['profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo formatCurrency($data['profit']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Tổng cộng</th>
                            <th><?php echo formatCurrency($total_sales); ?></th>
                            <th><?php echo formatCurrency($total_expenses); ?></th>
                            <th class="<?php echo $total_profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo formatCurrency($total_profit); ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="mt-4">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($profit_data)): ?>
        var profitData = {
            labels: [
                <?php
                foreach ($profit_data as $date => $data) {
                    switch ($group_by) {
                        case 'day':
                            echo "'" . date('d/m/Y', strtotime($date)) . "', ";
                            break;
                        case 'month':
                            echo "'" . date('m/Y', strtotime($date . '-01')) . "', ";
                            break;
                        case 'year':
                            echo "'" . $date . "', ";
                            break;
                    }
                }
                ?>
            ],
            datasets: [
                {
                    label: 'Doanh thu',
                    data: [<?php echo implode(', ', array_column($profit_data, 'sales')); ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Chi phí',
                    data: [<?php echo implode(', ', array_column($profit_data, 'expenses')); ?>],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Lợi nhuận',
                    data: [<?php echo implode(', ', array_column($profit_data, 'profit')); ?>],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    type: 'line'
                }
            ]
        };
        
        var profitCtx = document.getElementById('profitChart').getContext('2d');
        var profitChart = new Chart(profitCtx, {
            type: 'bar',
            data: profitData,
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
    });
</script>

<?php include 'includes/footer.php'; ?>
