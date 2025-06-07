<?php
/**
 * Customer Model
 * 
 * Handles customer-related database operations
 */

class Customer {
    // Database connection
    private $conn;
    
    // Customer properties
    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;
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
     * Get all customers
     * 
     * @return array
     */
    public function getAll() {
        $query = "SELECT * FROM customers ORDER BY name ASC";
        $result = $this->conn->query($query);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get customer by ID
     * 
     * @param int $id Customer ID
     * @return bool
     */
    public function getById($id) {
        $query = "SELECT * FROM customers WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create new customer
     * 
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $this->name, $this->email, $this->phone, $this->address);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        return false;
    }
    
    /**
     * Update customer
     * 
     * @return bool
     */
    public function update() {
        $query = "UPDATE customers SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $this->name, $this->email, $this->phone, $this->address, $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete customer
     * 
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM customers WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Search customers by name or phone
     * 
     * @param string $keyword Search keyword
     * @return array
     */
    public function search($keyword) {
        $query = "SELECT * FROM customers WHERE name LIKE ? OR phone LIKE ? ORDER BY name ASC";
        
        $search_term = "%{$keyword}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get customer purchase history
     * 
     * @return array
     */
    public function getPurchaseHistory() {
        $query = "SELECT so.*, u.name as user_name 
                  FROM sales_orders so 
                  LEFT JOIN users u ON so.user_id = u.id 
                  WHERE so.customer_id = ? 
                  ORDER BY so.order_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
