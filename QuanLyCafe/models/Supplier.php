<?php
/**
 * Supplier Model
 * 
 * Handles supplier-related database operations
 */

class Supplier {
    // Database connection
    private $conn;
    
    // Supplier properties
    public $id;
    public $name;
    public $contact_person;
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
     * Get all suppliers
     * 
     * @return array
     */
    public function getAll() {
        $query = "SELECT * FROM suppliers ORDER BY name ASC";
        $result = $this->conn->query($query);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get supplier by ID
     * 
     * @param int $id Supplier ID
     * @return bool
     */
    public function getById($id) {
        $query = "SELECT * FROM suppliers WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->contact_person = $row['contact_person'];
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
     * Create new supplier
     * 
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO suppliers (name, contact_person, email, phone, address) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssss", 
            $this->name, 
            $this->contact_person, 
            $this->email, 
            $this->phone, 
            $this->address
        );
        
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        return false;
    }
    
    /**
     * Update supplier
     * 
     * @return bool
     */
    public function update() {
        $query = "UPDATE suppliers 
                  SET name = ?, contact_person = ?, email = ?, phone = ?, address = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssi", 
            $this->name, 
            $this->contact_person, 
            $this->email, 
            $this->phone, 
            $this->address, 
            $this->id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete supplier
     * 
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM suppliers WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Search suppliers
     * 
     * @param string $keyword Search keyword
     * @return array
     */
    public function search($keyword) {
        $query = "SELECT * FROM suppliers 
                  WHERE name LIKE ? OR contact_person LIKE ? OR email LIKE ? OR phone LIKE ? 
                  ORDER BY name ASC";
        
        $search_term = "%{$keyword}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get supplier's ingredients
     * 
     * @return array
     */
    public function getIngredients() {
        $query = "SELECT * FROM ingredients WHERE supplier_id = ? ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get supplier's purchase orders
     * 
     * @return array
     */
    public function getPurchaseOrders() {
        $query = "SELECT * FROM purchase_orders WHERE supplier_id = ? ORDER BY order_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
