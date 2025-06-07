<?php
/**
 * SalesOrder Model
 *
 * Handles sales order-related database operations
 */

class SalesOrder {
    // Database connection
    private $conn;

    // SalesOrder properties
    public $id;
    public $order_number;
    public $customer_id;
    public $user_id;
    public $order_date;
    public $total_amount;
    public $status;
    public $payment_method;
    public $notes;
    public $created_at;
    public $updated_at;

    // Additional properties
    public $user_name;
    public $customer_name;

    /**
     * Constructor
     *
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all sales orders
     *
     * @return array
     */
    public function getAll() {
        $query = "SELECT so.*, c.name as customer_name, u.name as user_name
                  FROM sales_orders so
                  LEFT JOIN customers c ON so.customer_id = c.id
                  LEFT JOIN users u ON so.user_id = u.id
                  ORDER BY so.order_date DESC";

        $result = $this->conn->query($query);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get sales order by ID
     *
     * @param int $id Sales order ID
     * @return bool
     */
    public function getById($id) {
        $query = "SELECT so.*, c.name as customer_name, u.name as user_name
                  FROM sales_orders so
                  LEFT JOIN customers c ON so.customer_id = c.id
                  LEFT JOIN users u ON so.user_id = u.id
                  WHERE so.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            $this->id = $row['id'];
            $this->order_number = $row['order_number'];
            $this->customer_id = $row['customer_id'];
            $this->user_id = $row['user_id'];
            $this->order_date = $row['order_date'];
            $this->total_amount = $row['total_amount'];
            $this->status = $row['status'];
            $this->payment_method = $row['payment_method'];
            $this->notes = $row['notes'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            // Thêm thông tin bổ sung
            $this->user_name = $row['user_name'];
            $this->customer_name = $row['customer_name'];

            return true;
        }

        return false;
    }

    /**
     * Create new sales order
     *
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO sales_orders (order_number, customer_id, user_id, order_date, total_amount, status, payment_method, notes)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("siisdsss",
            $this->order_number,
            $this->customer_id,
            $this->user_id,
            $this->order_date,
            $this->total_amount,
            $this->status,
            $this->payment_method,
            $this->notes
        );

        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }

        return false;
    }

    /**
     * Update sales order
     *
     * @return bool
     */
    public function update() {
        $query = "UPDATE sales_orders
                  SET customer_id = ?, user_id = ?, order_date = ?, total_amount = ?,
                      status = ?, payment_method = ?, notes = ?
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("iisdssi",
            $this->customer_id,
            $this->user_id,
            $this->order_date,
            $this->total_amount,
            $this->status,
            $this->payment_method,
            $this->notes,
            $this->id
        );

        return $stmt->execute();
    }

    /**
     * Delete sales order
     *
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM sales_orders WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);

        return $stmt->execute();
    }

    /**
     * Get sales order items
     *
     * @return array
     */
    public function getItems() {
        $query = "SELECT soi.*, p.name as product_name, p.code as product_code
                  FROM sales_order_items soi
                  LEFT JOIN products p ON soi.product_id = p.id
                  WHERE soi.sales_order_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add sales order item
     *
     * @param int $product_id Product ID
     * @param int $quantity Quantity
     * @param float $unit_price Unit price
     * @return bool
     */
    public function addItem($product_id, $quantity, $unit_price) {
        $query = "INSERT INTO sales_order_items (sales_order_id, product_id, quantity, unit_price, total_price)
                  VALUES (?, ?, ?, ?, ?)";

        $total_price = $quantity * $unit_price;

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiddd", $this->id, $product_id, $quantity, $unit_price, $total_price);

        return $stmt->execute();
    }

    /**
     * Update product stock after sales
     *
     * @param int $product_id Product ID
     * @param int $quantity Quantity
     * @return bool
     */
    public function updateProductStock($product_id, $quantity) {
        $query = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $quantity, $product_id);

        return $stmt->execute();
    }

    /**
     * Generate unique order number
     *
     * @return string
     */
    public function generateOrderNumber() {
        return 'SO' . date('YmdHis') . rand(100, 999);
    }
}
