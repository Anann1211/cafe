<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chi tiết đơn hàng</h1>
        <div>
            <a href="index.php?page=sales-orders&action=print-invoice&id=<?php echo $sales_order->id; ?>" class="btn btn-info me-2" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> Xuất hóa đơn
            </a>
            <button class="btn btn-secondary me-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i> In trang này
            </button>
            <a href="index.php?page=sales-orders" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đơn hàng #<?php echo $sales_order->order_number; ?></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Thông tin chung</h6>
                            <p class="mb-1"><strong>Mã đơn hàng:</strong> <?php echo $sales_order->order_number; ?></p>
                            <p class="mb-1"><strong>Ngày tạo:</strong> <?php echo formatDate($sales_order->order_date); ?></p>
                            <p class="mb-1">
                                <strong>Trạng thái:</strong>
                                <?php if ($sales_order->status == 'completed'): ?>
                                    <span class="badge bg-success">Hoàn thành</span>
                                <?php elseif ($sales_order->status == 'pending'): ?>
                                    <span class="badge bg-warning">Đang xử lý</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Đã hủy</span>
                                <?php endif; ?>
                            </p>
                            <p class="mb-1">
                                <strong>Phương thức thanh toán:</strong>
                                <?php
                                switch ($sales_order->payment_method) {
                                    case 'cash':
                                        echo 'Tiền mặt';
                                        break;
                                    case 'card':
                                        echo 'Thẻ';
                                        break;
                                    case 'transfer':
                                        echo 'Chuyển khoản';
                                        break;
                                    default:
                                        echo $sales_order->payment_method;
                                }
                                ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Thông tin khách hàng</h6>
                            <?php if ($sales_order->customer_id): ?>
                                <?php
                                $customer = new Customer($conn);
                                $customer->getById($sales_order->customer_id);
                                ?>
                                <p class="mb-1"><strong>Tên khách hàng:</strong> <?php echo $customer->name; ?></p>
                                <p class="mb-1"><strong>Số điện thoại:</strong> <?php echo $customer->phone; ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?php echo $customer->email; ?></p>
                                <p class="mb-1"><strong>Địa chỉ:</strong> <?php echo $customer->address; ?></p>
                            <?php else: ?>
                                <p class="text-muted">Khách lẻ</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h6 class="fw-bold">Chi tiết đơn hàng</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($order_items)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Không có sản phẩm nào</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($order_items as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo $item['product_name']; ?></td>
                                            <td><?php echo formatCurrency($item['unit_price']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo formatCurrency($item['total_price']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Tổng cộng:</th>
                                    <th><?php echo formatCurrency($sales_order->total_amount); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?php if (!empty($sales_order->notes)): ?>
                        <div class="mt-3">
                            <h6 class="fw-bold">Ghi chú</h6>
                            <p><?php echo nl2br($sales_order->notes); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin bổ sung</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Nhân viên bán hàng:</strong> <?php echo isset($sales_order->user_name) ? $sales_order->user_name : 'N/A'; ?></p>
                    <p class="mb-1"><strong>Ngày tạo:</strong> <?php echo formatDate($sales_order->created_at); ?></p>
                    <p class="mb-1"><strong>Cập nhật lần cuối:</strong> <?php echo formatDate($sales_order->updated_at); ?></p>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="index.php?page=sales-orders" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tạo đơn hàng mới
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style media="print">
    @page {
        size: auto;
        margin: 10mm;
    }

    body {
        margin: 0;
        padding: 0;
    }

    .container-fluid {
        width: 100%;
        padding: 0;
    }

    .no-print {
        display: none !important;
    }

    .card {
        border: none !important;
    }

    .card-header {
        background-color: #fff !important;
        border-bottom: 1px solid #000 !important;
    }

    .btn, .sidebar, header, footer, nav {
        display: none !important;
    }
</style>

<?php include 'includes/footer.php'; ?>
