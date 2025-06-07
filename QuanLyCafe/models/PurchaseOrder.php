<?php
/**
 * PurchaseOrder Model
 * 
 * Handles purchase order-related database operations
 */

class PurchaseOrder {
    // Database connection
    private $conn;
    
    // PurchaseOrder properties
    public $id;
    public $order_number;
    public $supplier_id;
    public $user_id;
    public $order_date;
    public $total_amount;
    public $status;
    public $notes;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor
     * 
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all purchase orders
     * 
     * @return array
     */
    public function getAll() {
        $query = "SELECT po.*, s.name as supplier_name, u.name as user_name 
                  FROM purchase_orders po 
                  LEFT JOIN suppliers s ON po.supplier_id = s.id 
                  LEFT JOIN users u ON po.user_id = u.id 
                  ORDER BY po.order_date DESC";
        
        $result = $this->conn->query($query);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get purchase order by ID
     * 
     * @param int $id Purchase order ID
     * @return bool
     */
    public function getById($id) {
        $query = "SELECT po.*, s.name as supplier_name, u.name as user_name 
                  FROM purchase_orders po 
                  LEFT JOIN suppliers s ON po.supplier_id = s.id 
                  LEFT JOIN users u ON po.user_id = u.id 
                  WHERE po.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            $this->id = $row['id'];
            $this->order_number = $row['order_number'];
            $this->supplier_id = $row['supplier_id'];
            $this->supplier_name = $row['supplier_name'];
            $this->user_id = $row['user_id'];
            $this->user_name = $row['user_name'];
            $this->order_date = $row['order_date'];
            $this->total_amount = $row['total_amount'];
            $this->status = $row['status'];
            $this->notes = $row['notes'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create new purchase order
     * 
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO purchase_orders (order_number, supplier_id, user_id, order_date, total_amount, status, notes) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("siisdss", 
            $this->order_number, 
            $this->supplier_id, 
            $this->user_id, 
            $this->order_date, 
            $this->total_amount, 
            $this->status, 
            $this->notes
        );
        
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        return false;
    }
    
    /**
     * Update purchase order
     * 
     * @return bool
     */
    public function update() {
        $query = "UPDATE purchase_orders 
                  SET supplier_id = ?, user_id = ?, order_date = ?, total_amount = ?, status = ?, notes = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("iisdssi", 
            $this->supplier_id, 
            $this->user_id, 
            $this->order_date, 
            $this->total_amount, 
            $this->status, 
            $this->notes, 
            $this->id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete purchase order
     * 
     * @return bool
     */
    public function delete() {
        // First, delete all purchase order items
        $query = "DELETE FROM purchase_order_items WHERE purchase_order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        
        // Then, delete the purchase order
        $query = "DELETE FROM purchase_orders WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Get purchase order items
     * 
     * @return array
     */
    public function getItems() {
        $query = "SELECT poi.*, i.name as ingredient_name, i.unit 
                  FROM purchase_order_items poi 
                  LEFT JOIN ingredients i ON poi.ingredient_id = i.id 
                  WHERE poi.purchase_order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Add purchase order item
     * 
     * @param int $ingredient_id Ingredient ID
     * @param float $quantity Quantity
     * @param float $unit_price Unit price
     * @return bool
     */
    public function addItem($ingredient_id, $quantity, $unit_price) {
        $query = "INSERT INTO purchase_order_items (purchase_order_id, ingredient_id, quantity, unit_price, total_price) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $total_price = $quantity * $unit_price;
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiddd", $this->id, $ingredient_id, $quantity, $unit_price, $total_price);
        
        return $stmt->execute();
    }
    
    /**
     * Update ingredient stock after purchase
     * 
     * @param int $ingredient_id Ingredient ID
     * @param float $quantity Quantity
     * @return bool
     */
    public function updateIngredientStock($ingredient_id, $quantity) {
        $query = "UPDATE ingredients SET stock_quantity = stock_quantity + ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $quantity, $ingredient_id);
        
        return $stmt->execute();
    }
    
    /**
     * Generate unique order number
     * 
     * @return string
     */
    public function generateOrderNumber() {
        return 'PO' . date('YmdHis') . rand(100, 999);
    }
    
    /**
     * Get recent purchase orders
     * 
     * @param int $limit Number of records to return
     * @return array
     */
    public function getRecent($limit = 5) {
        $query = "SELECT po.*, s.name as supplier_name 
                  FROM purchase_orders po 
                  LEFT JOIN suppliers s ON po.supplier_id = s.id 
                  ORDER BY po.order_date DESC 
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
