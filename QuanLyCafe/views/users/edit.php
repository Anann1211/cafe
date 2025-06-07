<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chỉnh sửa nhân viên</h1>
        <a href="index.php?page=users" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="index.php?page=users&action=edit&id=<?php echo $user->id; ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user->name; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user->email; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted">Để trống nếu không muốn thay đổi mật khẩu. Mật khẩu mới phải có ít nhất 6 ký tự.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">-- Chọn vai trò --</option>
                                <option value="admin" <?php echo ($user->role == 'admin') ? 'selected' : ''; ?>>Quản lý</option>
                                <option value="staff" <?php echo ($user->role == 'staff') ? 'selected' : ''; ?>>Nhân viên</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" <?php echo ($user->status == 'active') ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="inactive" <?php echo ($user->status == 'inactive') ? 'selected' : ''; ?>>Không hoạt động</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $user->phone; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="gender" class="form-label">Giới tính</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male" <?php echo ($user->gender == 'male') ? 'selected' : ''; ?>>Nam</option>
                                <option value="female" <?php echo ($user->gender == 'female') ? 'selected' : ''; ?>>Nữ</option>
                                <option value="other" <?php echo ($user->gender == 'other') ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo $user->birth_date; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="hire_date" class="form-label">Ngày bắt đầu làm việc</label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo $user->hire_date; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo $user->address; ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Cập nhật nhân viên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
