/**
 * VietAn Coffee Shop Management System - Custom JavaScript
 */

// Document ready
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
    
    // Confirm delete
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Bạn có chắc chắn muốn xóa?')) {
            e.preventDefault();
        }
    });
    
    // Add to cart functionality for sales
    $('.btn-add-to-cart').on('click', function() {
        var productId = $(this).data('product-id');
        var productName = $(this).data('product-name');
        var productPrice = $(this).data('product-price');
        
        addToCart(productId, productName, productPrice);
    });
    
    // Update cart item quantity
    $(document).on('change', '.cart-item-quantity', function() {
        var productId = $(this).data('product-id');
        var quantity = $(this).val();
        
        updateCartItemQuantity(productId, quantity);
    });
    
    // Remove cart item
    $(document).on('click', '.btn-remove-cart-item', function() {
        var productId = $(this).data('product-id');
        
        removeCartItem(productId);
    });
});

/**
 * Add product to cart
 * 
 * @param {number} productId 
 * @param {string} productName 
 * @param {number} productPrice 
 */
function addToCart(productId, productName, productPrice) {
    $.ajax({
        url: 'index.php?page=sales-orders&action=add-to-cart',
        type: 'POST',
        data: {
            product_id: productId,
            product_name: productName,
            product_price: productPrice,
            quantity: 1
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateCartDisplay(response.cart);
                showNotification('Đã thêm sản phẩm vào giỏ hàng', 'success');
            } else {
                showNotification(response.message, 'danger');
            }
        },
        error: function() {
            showNotification('Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng', 'danger');
        }
    });
}

/**
 * Update cart item quantity
 * 
 * @param {number} productId 
 * @param {number} quantity 
 */
function updateCartItemQuantity(productId, quantity) {
    $.ajax({
        url: 'index.php?page=sales-orders&action=update-cart',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateCartDisplay(response.cart);
                showNotification('Đã cập nhật giỏ hàng', 'success');
            } else {
                showNotification(response.message, 'danger');
            }
        },
        error: function() {
            showNotification('Đã xảy ra lỗi khi cập nhật giỏ hàng', 'danger');
        }
    });
}

/**
 * Remove cart item
 * 
 * @param {number} productId 
 */
function removeCartItem(productId) {
    $.ajax({
        url: 'index.php?page=sales-orders&action=remove-from-cart',
        type: 'POST',
        data: {
            product_id: productId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateCartDisplay(response.cart);
                showNotification('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
            } else {
                showNotification(response.message, 'danger');
            }
        },
        error: function() {
            showNotification('Đã xảy ra lỗi khi xóa sản phẩm khỏi giỏ hàng', 'danger');
        }
    });
}

/**
 * Update cart display
 * 
 * @param {object} cart 
 */
function updateCartDisplay(cart) {
    var cartTable = $('#cart-items');
    var cartTotal = $('#cart-total');
    
    // Clear cart table
    cartTable.empty();
    
    // Add cart items
    if (cart.items.length > 0) {
        $.each(cart.items, function(index, item) {
            var row = $('<tr>');
            
            row.append($('<td>').text(index + 1));
            row.append($('<td>').text(item.name));
            row.append($('<td>').text(formatCurrency(item.price)));
            
            var quantityInput = $('<input>')
                .attr('type', 'number')
                .attr('min', '1')
                .attr('class', 'form-control form-control-sm cart-item-quantity')
                .attr('data-product-id', item.id)
                .val(item.quantity);
            
            row.append($('<td>').append(quantityInput));
            row.append($('<td>').text(formatCurrency(item.price * item.quantity)));
            
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
    
    // Update cart total
    cartTotal.text(formatCurrency(cart.total));
}

/**
 * Format currency
 * 
 * @param {number} amount 
 * @returns {string}
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

/**
 * Show notification
 * 
 * @param {string} message 
 * @param {string} type 
 */
function showNotification(message, type) {
    var notification = $('<div>')
        .addClass('alert alert-' + type + ' alert-dismissible fade show')
        .attr('role', 'alert')
        .html(message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
    
    $('#notification-container').append(notification);
    
    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}
