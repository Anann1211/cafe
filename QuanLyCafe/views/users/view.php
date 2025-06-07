<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Thông tin nhân viên</h1>
        <div>
            <a href="index.php?page=users&action=edit&id=<?php echo $user->id; ?>" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Chỉnh sửa
            </a>
            <a href="index.php?page=users" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if ($user->gender == 'female'): ?>
                            <i class="fas fa-user-circle fa-6x text-primary"></i>
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-6x text-primary"></i>
                        <?php endif; ?>
                    </div>
                    
                    <h4><?php echo $user->name; ?></h4>
                    <p class="text-muted"><?php echo $user->email; ?></p>
                    
                    <div class="mb-2">
                        <?php if ($user->role == 'admin'): ?>
                            <span class="badge bg-danger">Quản lý</span>
                        <?php else: ?>
                            <span class="badge bg-info">Nhân viên</span>
                        <?php endif; ?>
                        
                        <?php if ($user->status == 'active'): ?>
                            <span class="badge bg-success">Hoạt động</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Không hoạt động</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($user->phone): ?>
                        <p><i class="fas fa-phone me-2"></i> <?php echo $user->phone; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin chi tiết</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">ID</th>
                                <td><?php echo $user->id; ?></td>
                            </tr>
                            <tr>
                                <th>Họ tên</th>
                                <td><?php echo $user->name; ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo $user->email; ?></td>
                            </tr>
                            <tr>
                                <th>Vai trò</th>
                                <td>
                                    <?php if ($user->role == 'admin'): ?>
                                        <span class="badge bg-danger">Quản lý</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Nhân viên</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    <?php if ($user->status == 'active'): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td><?php echo $user->phone ? $user->phone : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Giới tính</th>
                                <td>
                                    <?php
                                    switch ($user->gender) {
                                        case 'male':
                                            echo 'Nam';
                                            break;
                                        case 'female':
                                            echo 'Nữ';
                                            break;
                                        case 'other':
                                            echo 'Khác';
                                            break;
                                        default:
                                            echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày sinh</th>
                                <td><?php echo $user->birth_date ? formatDate($user->birth_date) : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Ngày bắt đầu làm việc</th>
                                <td><?php echo $user->hire_date ? formatDate($user->hire_date) : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Địa chỉ</th>
                                <td><?php echo $user->address ? $user->address : 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td><?php echo formatDate($user->created_at); ?></td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối</th>
                                <td><?php echo formatDate($user->updated_at); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php
            // Lấy thông tin đơn hàng của nhân viên
            $sales_query = "
                SELECT COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_amount
                FROM sales_orders
                WHERE user_id = ? AND status = 'completed'
            ";
            $stmt = $conn->prepare($sales_query);
            $stmt->bind_param("i", $user->id);
            $stmt->execute();
            $sales_result = $stmt->get_result()->fetch_assoc();
            
            // Lấy thông tin đơn nhập hàng của nhân viên
            $purchase_query = "
                SELECT COUNT(*) as total_purchases, COALESCE(SUM(total_amount), 0) as total_amount
                FROM purchase_orders
                WHERE user_id = ? AND status = 'completed'
            ";
            $stmt = $conn->prepare($purchase_query);
            $stmt->bind_param("i", $user->id);
            $stmt->execute();
            $purchase_result = $stmt->get_result()->fetch_assoc();
            ?>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thống kê hoạt động</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Đơn hàng bán</h6>
                                    <p class="card-text">Số lượng: <?php echo $sales_result['total_sales']; ?></p>
                                    <p class="card-text">Tổng tiền: <?php echo formatCurrency($sales_result['total_amount']); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Đơn hàng nhập</h6>
                                    <p class="card-text">Số lượng: <?php echo $purchase_result['total_purchases']; ?></p>
                                    <p class="card-text">Tổng tiền: <?php echo formatCurrency($purchase_result['total_amount']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
