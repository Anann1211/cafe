<?php
/**
 * Product Model
 * 
 * Handles product-related database operations
 */

class Product {
    // Database connection
    private $conn;
    
    // Product properties
    public $id;
    public $code;
    public $name;
    public $category_id;
    public $type;
    public $size;
    public $price;
    public $description;
    public $stock_quantity;
    public $image;
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
     * Get all products
     * 
     * @return array
     */
    public function getAll() {
        $query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  ORDER BY p.name ASC";
        
        $result = $this->conn->query($query);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get product by ID
     * 
     * @param int $id Product ID
     * @return bool
     */
    public function getById($id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            $this->id = $row['id'];
            $this->code = $row['code'];
            $this->name = $row['name'];
            $this->category_id = $row['category_id'];
            $this->type = $row['type'];
            $this->size = $row['size'];
            $this->price = $row['price'];
            $this->description = $row['description'];
            $this->stock_quantity = $row['stock_quantity'];
            $this->image = $row['image'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create new product
     * 
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO products (code, name, category_id, type, size, price, description, stock_quantity, image) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("ssissdsis", 
            $this->code, 
            $this->name, 
            $this->category_id, 
            $this->type, 
            $this->size, 
            $this->price, 
            $this->description, 
            $this->stock_quantity, 
            $this->image
        );
        
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        return false;
    }
    
    /**
     * Update product
     * 
     * @return bool
     */
    public function update() {
        $query = "UPDATE products 
                  SET code = ?, name = ?, category_id = ?, type = ?, size = ?, 
                      price = ?, description = ?, stock_quantity = ?, image = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("ssissdsisi", 
            $this->code, 
            $this->name, 
            $this->category_id, 
            $this->type, 
            $this->size, 
            $this->price, 
            $this->description, 
            $this->stock_quantity, 
            $this->image, 
            $this->id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete product
     * 
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM products WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Check if product code exists
     * 
     * @param string $code Product code to check
     * @param int $exclude_id Product ID to exclude from check (for updates)
     * @return bool
     */
    public function codeExists($code, $exclude_id = 0) {
        $query = "SELECT id FROM products WHERE code = ? AND id != ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $code, $exclude_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    /**
     * Update product stock
     * 
     * @param int $quantity Quantity to add (positive) or subtract (negative)
     * @return bool
     */
    public function updateStock($quantity) {
        $query = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $quantity, $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Get products with low stock
     * 
     * @param int $threshold Stock threshold
     * @return array
     */
    public function getLowStock($threshold = 10) {
        $query = "SELECT * FROM products WHERE stock_quantity <= ? ORDER BY stock_quantity ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $threshold);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Search products
     * 
     * @param string $keyword Search keyword
     * @return array
     */
    public function search($keyword) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.name LIKE ? OR p.code LIKE ? OR p.description LIKE ? 
                  ORDER BY p.name ASC";
        
        $search_term = "%{$keyword}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $search_term, $search_term, $search_term);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get products by category
     * 
     * @param int $category_id Category ID
     * @return array
     */
    public function getByCategory($category_id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.category_id = ? 
                  ORDER BY p.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
