<?php
/**
 * PDF Generator
 * 
 * Tạo file PDF cho hóa đơn
 */

// Kiểm tra nếu thư viện TCPDF chưa được include
if (!class_exists('TCPDF')) {
    // Nếu chưa có thư viện TCPDF, sử dụng HTML để tạo hóa đơn có thể in
    class PDFGenerator {
        /**
         * Tạo hóa đơn HTML
         * 
         * @param array $order Thông tin đơn hàng
         * @param array $order_items Danh sách sản phẩm trong đơn hàng
         * @param array $customer Thông tin khách hàng (nếu có)
         * @return string HTML content
         */
        public static function generateInvoice($order, $order_items, $customer = null) {
            $html = '
            <!DOCTYPE html>
            <html lang="vi">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Hóa đơn #' . $order['order_number'] . '</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 20px;
                        font-size: 14px;
                    }
                    .invoice-header {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .invoice-title {
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .invoice-subtitle {
                        font-size: 16px;
                        margin-bottom: 5px;
                    }
                    .invoice-info {
                        margin-bottom: 20px;
                    }
                    .invoice-info-row {
                        margin-bottom: 5px;
                    }
                    .invoice-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    .invoice-table th, .invoice-table td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    .invoice-table th {
                        background-color: #f2f2f2;
                    }
                    .invoice-total {
                        text-align: right;
                        margin-bottom: 20px;
                    }
                    .invoice-footer {
                        text-align: center;
                        margin-top: 30px;
                        font-size: 12px;
                    }
                    @media print {
                        body {
                            padding: 0;
                            margin: 0;
                        }
                        .no-print {
                            display: none;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="invoice-header">
                    <div class="invoice-title">VietAn Coffee</div>
                    <div class="invoice-subtitle">Hóa đơn bán hàng</div>
                    <div>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. HCM</div>
                    <div>Điện thoại: 0123 456 789</div>
                </div>
                
                <div class="invoice-info">
                    <div class="invoice-info-row"><strong>Mã hóa đơn:</strong> ' . $order['order_number'] . '</div>
                    <div class="invoice-info-row"><strong>Ngày:</strong> ' . date('d/m/Y', strtotime($order['order_date'])) . '</div>
                    <div class="invoice-info-row"><strong>Nhân viên:</strong> ' . (isset($order['user_name']) ? $order['user_name'] : 'N/A') . '</div>';
            
            if ($customer) {
                $html .= '
                    <div class="invoice-info-row"><strong>Khách hàng:</strong> ' . $customer['name'] . '</div>
                    <div class="invoice-info-row"><strong>Điện thoại:</strong> ' . $customer['phone'] . '</div>';
            } else {
                $html .= '
                    <div class="invoice-info-row"><strong>Khách hàng:</strong> Khách lẻ</div>';
            }
            
            $html .= '
                </div>
                
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $total = 0;
            foreach ($order_items as $index => $item) {
                $html .= '
                        <tr>
                            <td>' . ($index + 1) . '</td>
                            <td>' . $item['product_name'] . '</td>
                            <td>' . number_format($item['unit_price'], 0, ',', '.') . ' VNĐ</td>
                            <td>' . $item['quantity'] . '</td>
                            <td>' . number_format($item['total_price'], 0, ',', '.') . ' VNĐ</td>
                        </tr>';
                $total += $item['total_price'];
            }
            
            $html .= '
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align: right;">Tổng cộng:</th>
                            <th>' . number_format($total, 0, ',', '.') . ' VNĐ</th>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="invoice-total">
                    <div><strong>Phương thức thanh toán:</strong> ';
            
            switch ($order['payment_method']) {
                case 'cash':
                    $html .= 'Tiền mặt';
                    break;
                case 'card':
                    $html .= 'Thẻ';
                    break;
                case 'transfer':
                    $html .= 'Chuyển khoản';
                    break;
                default:
                    $html .= $order['payment_method'];
            }
            
            $html .= '</div>
                </div>';
            
            if (!empty($order['notes'])) {
                $html .= '
                <div class="invoice-info">
                    <div class="invoice-info-row"><strong>Ghi chú:</strong> ' . nl2br($order['notes']) . '</div>
                </div>';
            }
            
            $html .= '
                <div class="invoice-footer">
                    <p>Cảm ơn quý khách đã sử dụng dịch vụ của VietAn Coffee!</p>
                    <p>Hẹn gặp lại quý khách lần sau.</p>
                </div>
                
                <div class="no-print" style="text-align: center; margin-top: 20px;">
                    <button onclick="window.print()">In hóa đơn</button>
                    <button onclick="window.close()">Đóng</button>
                </div>
            </body>
            </html>';
            
            return $html;
        }
    }
}
