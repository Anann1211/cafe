<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Bán hàng</h1>
        <div>
            <a href="index.php?page=sales-orders&action=clear-cart" class="btn btn-warning me-2" onclick="return confirm('Bạn có chắc chắn muốn xóa giỏ hàng?')">
                <i class="fas fa-trash me-1"></i> Xóa giỏ hàng
            </a>
            <a href="index.php?page=sales-orders&action=checkout" class="btn btn-primary">
                <i class="fas fa-shopping-cart me-1"></i> Thanh toán
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Danh sách sản phẩm -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Danh sách sản phẩm</h5>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" id="search-product" placeholder="Tìm kiếm sản phẩm...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="product-list">
                        <?php if (empty($products)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">Không có sản phẩm nào</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-4 col-lg-3 mb-4 product-item">
                                    <div class="card h-100 product-card">
                                        <div class="position-relative">
                                            <?php if (!empty($product['image']) && file_exists('assets/images/products/' . $product['image'])): ?>
                                                <img src="assets/images/products/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>" style="height: 150px; object-fit: cover;">
                                            <?php else: ?>
                                                <img src="assets/images/no-image.jpg" class="card-img-top" alt="No Image" style="height: 150px; object-fit: cover;">
                                            <?php endif; ?>

                                            <?php if ($product['stock_quantity'] <= 0): ?>
                                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background-color: rgba(0,0,0,0.5);">
                                                    <span class="badge bg-danger">Hết hàng</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo $product['name']; ?></h6>
                                            <p class="card-text text-primary fw-bold"><?php echo formatCurrency($product['price']); ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <?php
                                                    switch ($product['type']) {
                                                        case 'phin':
                                                            echo 'Phin';
                                                            break;
                                                        case 'machine':
                                                            echo 'Máy';
                                                            break;
                                                        case 'instant':
                                                            echo 'Hòa tan';
                                                            break;
                                                        default:
                                                            echo $product['type'];
                                                    }
                                                    ?> /
                                                    <?php
                                                    switch ($product['size']) {
                                                        case 'small':
                                                            echo 'Nhỏ';
                                                            break;
                                                        case 'medium':
                                                            echo 'Vừa';
                                                            break;
                                                        case 'large':
                                                            echo 'Lớn';
                                                            break;
                                                        default:
                                                            echo $product['size'];
                                                    }
                                                    ?>
                                                </small>
                                                <small class="text-muted">Còn: <?php echo $product['stock_quantity']; ?></small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent border-top-0">
                                            <div class="d-flex">
                                                <input type="number" class="form-control form-control-sm me-2 product-quantity" min="1" max="<?php echo $product['stock_quantity']; ?>" value="1" <?php echo ($product['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
                                                <button type="button" class="btn btn-primary btn-sm w-100 btn-add-to-cart" data-product-id="<?php echo $product['id']; ?>" data-product-name="<?php echo $product['name']; ?>" data-product-price="<?php echo $product['price']; ?>" <?php echo ($product['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
                                                    <i class="fas fa-plus me-1"></i> Thêm
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Giỏ hàng -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header">
                    <h5 class="mb-0">Giỏ hàng</h5>
                </div>
                <div class="card-body">
                    <div id="notification-container"></div>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>SL</th>
                                    <th>Thành tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <?php if (empty($_SESSION['cart']['items'])): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Giỏ hàng trống</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($_SESSION['cart']['items'] as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo $item['name']; ?></td>
                                            <td><?php echo formatCurrency($item['price']); ?></td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm cart-item-quantity" min="1" value="<?php echo $item['quantity']; ?>" data-product-id="<?php echo $item['id']; ?>" style="width: 60px;">
                                            </td>
                                            <td><?php echo formatCurrency($item['total']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger btn-remove-cart-item" data-product-id="<?php echo $item['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Tổng cộng:</th>
                                    <th id="cart-total"><?php echo formatCurrency($_SESSION['cart']['total']); ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <a href="index.php?page=sales-orders&action=checkout" class="btn btn-success <?php echo empty($_SESSION['cart']['items']) ? 'disabled' : ''; ?>">
                            <i class="fas fa-check me-1"></i> Thanh toán
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Tìm kiếm sản phẩm
        $("#search-product").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".product-item").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Thêm sản phẩm vào giỏ hàng
        $(".btn-add-to-cart").on("click", function() {
            var productId = $(this).data("product-id");
            var productName = $(this).data("product-name");
            var productPrice = $(this).data("product-price");
            var quantity = $(this).closest(".card-footer").find(".product-quantity").val();

            $.ajax({
                url: "index.php?page=sales-orders&action=add-to-cart",
                type: "POST",
                data: {
                    product_id: productId,
                    product_name: productName,
                    product_price: productPrice,
                    quantity: quantity
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        updateCartDisplay(response.cart);
                        showNotification("Đã thêm sản phẩm vào giỏ hàng", "success");
                    } else {
                        showNotification(response.message, "danger");
                    }
                },
                error: function() {
                    showNotification("Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng", "danger");
                }
            });
        });

        // Cập nhật số lượng sản phẩm trong giỏ hàng
        $(document).on("change", ".cart-item-quantity", function() {
            var productId = $(this).data("product-id");
            var quantity = $(this).val();

            $.ajax({
                url: "index.php?page=sales-orders&action=update-cart",
                type: "POST",
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        updateCartDisplay(response.cart);
                        showNotification("Đã cập nhật giỏ hàng", "success");
                    } else {
                        showNotification(response.message, "danger");
                    }
                },
                error: function() {
                    showNotification("Đã xảy ra lỗi khi cập nhật giỏ hàng", "danger");
                }
            });
        });

        // Xóa sản phẩm khỏi giỏ hàng
        $(document).on("click", ".btn-remove-cart-item", function() {
            var productId = $(this).data("product-id");

            $.ajax({
                url: "index.php?page=sales-orders&action=remove-from-cart",
                type: "POST",
                data: {
                    product_id: productId
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        updateCartDisplay(response.cart);
                        showNotification("Đã xóa sản phẩm khỏi giỏ hàng", "success");
                    } else {
                        showNotification(response.message, "danger");
                    }
                },
                error: function() {
                    showNotification("Đã xảy ra lỗi khi xóa sản phẩm khỏi giỏ hàng", "danger");
                }
            });
        });

        /**
         * Cập nhật hiển thị giỏ hàng
         *
         * @param {object} cart Dữ liệu giỏ hàng
         */
        function updateCartDisplay(cart) {
            var cartTable = $('#cart-items');
            var cartTotal = $('#cart-total');

            // Xóa nội dung giỏ hàng hiện tại
            cartTable.empty();

            // Thêm các sản phẩm vào giỏ hàng
            if (cart.items.length > 0) {
                $.each(cart.items, function(index, item) {
                    var row = $('<tr>');

                    row.append($('<td>').text(index + 1));
                    row.append($('<td>').text(item.name));
                    row.append($('<td>').text(formatCurrency(item.price)));

                    var quantityInput = $('<input>')
                        .attr('type', 'number')
                        .attr('class', 'form-control form-control-sm cart-item-quantity')
                        .attr('min', '1')
                        .attr('data-product-id', item.id)
                        .attr('style', 'width: 60px;')
                        .val(item.quantity);

                    row.append($('<td>').append(quantityInput));
                    row.append($('<td>').text(formatCurrency(item.total)));

                    var removeButton = $('<button>')
                        .attr('type', 'button')
                        .attr('class', 'btn btn-sm btn-danger btn-remove-cart-item')
                        .attr('data-product-id', item.id)
                        .html('<i class="fas fa-trash"></i>');

                    row.append($('<td>').append(removeButton));

                    cartTable.append(row);
                });
            } else {
                cartTable.append($('<tr>').append($('<td colspan="6" class="text-center">').text('Giỏ hàng trống')));
            }

            // Cập nhật tổng tiền
            cartTotal.text(formatCurrency(cart.total));

            // Cập nhật nút thanh toán
            if (cart.items.length > 0) {
                $('.btn-success').removeClass('disabled');
            } else {
                $('.btn-success').addClass('disabled');
            }
        }

        /**
         * Hiển thị thông báo
         *
         * @param {string} message Nội dung thông báo
         * @param {string} type Loại thông báo (success, danger, warning, info)
         */
        function showNotification(message, type) {
            var notification = $('<div>')
                .addClass('alert alert-' + type + ' alert-dismissible fade show')
                .attr('role', 'alert')
                .html(message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');

            $('#notification-container').append(notification);

            // Tự động ẩn thông báo sau 3 giây
            setTimeout(function() {
                notification.alert('close');
            }, 3000);
        }

        /**
         * Định dạng tiền tệ
         *
         * @param {number} amount Số tiền
         * @returns {string} Chuỗi tiền tệ đã định dạng
         */
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
