<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Quản lý hóa đơn bán hàng</h1>
        <a href="index.php?page=sales-orders" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tạo đơn hàng mới
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Tìm kiếm hóa đơn</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="page" value="invoices">
                <input type="hidden" name="action" value="search">
                
                <div class="col-md-3">
                    <label for="keyword" class="form-label">Từ khóa</label>
                    <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Mã hóa đơn, tên khách hàng, SĐT..." value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>">
                </div>
                
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? $_GET['date_from'] : ''; ?>">
                </div>
                
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo isset($_GET['date_to']) ? $_GET['date_to'] : ''; ?>">
                </div>
                
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Hoàn thành</option>
                        <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Đang xử lý</option>
                        <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Tìm kiếm
                    </button>
                    <a href="index.php?page=invoices" class="btn btn-secondary">
                        <i class="fas fa-sync-alt me-1"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Mã hóa đơn</th>
                            <th>Ngày bán</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Phương thức thanh toán</th>
                            <th>Nhân viên</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invoices)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td><?php echo $invoice['order_number']; ?></td>
                                    <td><?php echo formatDate($invoice['order_date']); ?></td>
                                    <td>
                                        <?php if ($invoice['customer_id']): ?>
                                            <a href="index.php?page=customers&action=view&id=<?php echo $invoice['customer_id']; ?>">
                                                <?php echo $invoice['customer_name']; ?>
                                            </a>
                                        <?php else: ?>
                                            Khách lẻ
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo formatCurrency($invoice['total_amount']); ?></td>
                                    <td>
                                        <?php if ($invoice['status'] == 'completed'): ?>
                                            <span class="badge bg-success">Hoàn thành</span>
                                        <?php elseif ($invoice['status'] == 'pending'): ?>
                                            <span class="badge bg-warning">Đang xử lý</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Đã hủy</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        switch ($invoice['payment_method']) {
                                            case 'cash':
                                                echo '<span class="badge bg-info">Tiền mặt</span>';
                                                break;
                                            case 'card':
                                                echo '<span class="badge bg-primary">Thẻ</span>';
                                                break;
                                            case 'transfer':
                                                echo '<span class="badge bg-secondary">Chuyển khoản</span>';
                                                break;
                                            default:
                                                echo $invoice['payment_method'];
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $invoice['user_name']; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?page=invoices&action=view&id=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?page=invoices&action=print&id=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="In hóa đơn" target="_blank">
                                                <i class="fas fa-print"></i>
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
