<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse" id="sidebarMenu">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <h5 class="text-white">VietAn Coffee</h5>
            <p class="text-white-50 small mb-0">
                <?php if (isAdmin()): ?>
                <span class="badge bg-danger">Quản lý</span>
                <?php else: ?>
                <span class="badge bg-info">Nhân viên</span>
                <?php endif; ?>
                <?php echo getCurrentUserName(); ?>
            </p>
        </div>

        <ul class="nav flex-column">
            <!-- Menu chung cho cả Admin và Nhân viên -->
            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'dashboard' || !isset($_GET['page'])) ? 'active' : ''; ?>"
                    href="index.php?page=dashboard">
                    <i class="fas fa-tachometer-alt me-2"></i> Bảng điều khiển
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'sales-orders') ? 'active' : ''; ?>"
                    href="index.php?page=sales-orders">
                    <i class="fas fa-shopping-cart me-2"></i> Bán hàng
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'invoices') ? 'active' : ''; ?>"
                    href="index.php?page=invoices">
                    <i class="fas fa-file-invoice me-2"></i> Hóa đơn
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'products') ? 'active' : ''; ?>"
                    href="index.php?page=products">
                    <i class="fas fa-coffee me-2"></i> Sản phẩm
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'customers') ? 'active' : ''; ?>"
                    href="index.php?page=customers">
                    <i class="fas fa-users me-2"></i> Khách hàng
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'reports') ? 'active' : ''; ?>"
                    href="index.php?page=reports">
                    <i class="fas fa-chart-bar me-2"></i> Báo cáo & Thống kê
                </a>
            </li>

            <?php if (isAdmin()): ?>
            <!-- Menu chỉ dành cho Admin -->
            <!-- <li class="nav-item mt-3">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Quản lý hệ thống</span>
                </h6>
            </li> -->

            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'ingredients') ? 'active' : ''; ?>"
                    href="index.php?page=ingredients">
                    <i class="fas fa-boxes me-2"></i> Nguyên liệu
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'purchase-orders') ? 'active' : ''; ?>"
                    href="index.php?page=purchase-orders">
                    <i class="fas fa-truck me-2"></i> Nhập hàng
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'suppliers') ? 'active' : ''; ?>"
                    href="index.php?page=suppliers">
                    <i class="fas fa-building me-2"></i> Nhà cung cấp
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white <?php echo ($_GET['page'] == 'users') ? 'active' : ''; ?>"
                    href="index.php?page=users">
                    <i class="fas fa-user-cog me-2"></i> Nhân viên
                </a>
            </li>


            <?php endif; ?>
        </ul>
    </div>
</div>