<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Quản lý nhân viên</h1>
        <a href="index.php?page=users&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Thêm nhân viên
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Số điện thoại</th>
                            <th>Ngày bắt đầu</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['email']; ?></td>
                                    <td>
                                        <?php if ($item['role'] == 'admin'): ?>
                                            <span class="badge bg-danger">Quản lý</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Nhân viên</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $item['phone'] ? $item['phone'] : 'N/A'; ?></td>
                                    <td><?php echo $item['hire_date'] ? formatDate($item['hire_date']) : 'N/A'; ?></td>
                                    <td>
                                        <?php if ($item['status'] == 'active'): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?page=users&action=view&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?page=users&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($item['id'] != getCurrentUserId()): ?>
                                                <a href="index.php?page=users&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
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
