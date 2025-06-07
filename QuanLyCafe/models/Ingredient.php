<?php
/**
 * Ingredient Model
 * 
 * Handles ingredient-related database operations
 */

class Ingredient {
    // Database connection
    private $conn;
    
    // Ingredient properties
    public $id;
    public $name;
    public $unit;
    public $stock_quantity;
    public $price_per_unit;
    public $supplier_id;
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
     * Get all ingredients
     * 
     * @return array
     */
    public function getAll() {
        $query = "SELECT i.*, s.name as supplier_name 
                  FROM ingredients i 
                  LEFT JOIN suppliers s ON i.supplier_id = s.id 
                  ORDER BY i.name ASC";
        
        $result = $this->conn->query($query);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get ingredient by ID
     * 
     * @param int $id Ingredient ID
     * @return bool
     */
    public function getById($id) {
        $query = "SELECT i.*, s.name as supplier_name 
                  FROM ingredients i 
                  LEFT JOIN suppliers s ON i.supplier_id = s.id 
                  WHERE i.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->unit = $row['unit'];
            $this->stock_quantity = $row['stock_quantity'];
            $this->price_per_unit = $row['price_per_unit'];
            $this->supplier_id = $row['supplier_id'];
            $this->supplier_name = $row['supplier_name'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create new ingredient
     * 
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO ingredients (name, unit, stock_quantity, price_per_unit, supplier_id) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("ssddi", 
            $this->name, 
            $this->unit, 
            $this->stock_quantity, 
            $this->price_per_unit, 
            $this->supplier_id
        );
        
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        return false;
    }
    
    /**
     * Update ingredient
     * 
     * @return bool
     */
    public function update() {
        $query = "UPDATE ingredients 
                  SET name = ?, unit = ?, stock_quantity = ?, price_per_unit = ?, supplier_id = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("ssddii", 
            $this->name, 
            $this->unit, 
            $this->stock_quantity, 
            $this->price_per_unit, 
            $this->supplier_id, 
            $this->id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete ingredient
     * 
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM ingredients WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Update ingredient stock
     * 
     * @param float $quantity Quantity to add (positive) or subtract (negative)
     * @return bool
     */
    public function updateStock($quantity) {
        $query = "UPDATE ingredients SET stock_quantity = stock_quantity + ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $quantity, $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Get ingredients with low stock
     * 
     * @param float $threshold Stock threshold
     * @return array
     */
    public function getLowStock($threshold = 10) {
        $query = "SELECT i.*, s.name as supplier_name 
                  FROM ingredients i 
                  LEFT JOIN suppliers s ON i.supplier_id = s.id 
                  WHERE i.stock_quantity <= ? 
                  ORDER BY i.stock_quantity ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("d", $threshold);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Search ingredients
     * 
     * @param string $keyword Search keyword
     * @return array
     */
    public function search($keyword) {
        $query = "SELECT i.*, s.name as supplier_name 
                  FROM ingredients i 
                  LEFT JOIN suppliers s ON i.supplier_id = s.id 
                  WHERE i.name LIKE ? OR i.unit LIKE ? 
                  ORDER BY i.name ASC";
        
        $search_term = "%{$keyword}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
