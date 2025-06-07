<?php
/**
 * User Model
 * 
 * Handles user-related database operations
 */

class User {
    // Database connection
    private $conn;
    
    // User properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $phone;
    public $address;
    public $gender;
    public $birth_date;
    public $hire_date;
    public $status;
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
     * Get all users
     * 
     * @return array
     */
    public function getAll() {
        $query = "SELECT * FROM users ORDER BY name ASC";
        $result = $this->conn->query($query);
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return bool
     */
    public function getById($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->gender = $row['gender'];
            $this->birth_date = $row['birth_date'];
            $this->hire_date = $row['hire_date'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create new user
     * 
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO users (name, email, password, role, phone, address, gender, birth_date, hire_date, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bind_param("ssssssssss", 
            $this->name, 
            $this->email, 
            $hashed_password, 
            $this->role, 
            $this->phone, 
            $this->address, 
            $this->gender, 
            $this->birth_date, 
            $this->hire_date, 
            $this->status
        );
        
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        
        return false;
    }
    
    /**
     * Update user
     * 
     * @return bool
     */
    public function update() {
        $query = "UPDATE users 
                  SET name = ?, email = ?, role = ?, phone = ?, address = ?, 
                      gender = ?, birth_date = ?, hire_date = ?, status = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("sssssssssi", 
            $this->name, 
            $this->email, 
            $this->role, 
            $this->phone, 
            $this->address, 
            $this->gender, 
            $this->birth_date, 
            $this->hire_date, 
            $this->status, 
            $this->id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Update user password
     * 
     * @param string $new_password New password
     * @return bool
     */
    public function updatePassword($new_password) {
        $query = "UPDATE users SET password = ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt->bind_param("si", $hashed_password, $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete user
     * 
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM users WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Check if email exists
     * 
     * @param string $email Email to check
     * @param int $exclude_id User ID to exclude from check (for updates)
     * @return bool
     */
    public function emailExists($email, $exclude_id = 0) {
        $query = "SELECT id FROM users WHERE email = ? AND id != ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $email, $exclude_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    /**
     * Authenticate user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return bool
     */
    public function authenticate($email, $password) {
        $query = "SELECT id, name, email, password, role FROM users WHERE email = ? AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->role = $row['role'];
                
                return true;
            }
        }
        
        return false;
    }
}
