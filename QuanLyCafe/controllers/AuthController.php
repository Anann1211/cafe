<?php
/**
 * Authentication Controller
 * 
 * Handles user authentication (login, logout)
 */

// Include database connection
$conn = require_once 'config/database.php';

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle actions
switch ($action) {
    case 'logout':
        // Destroy session and redirect to login page
        session_destroy();
        redirect('login');
        break;
    
    default:
        // Check if user is already logged in
        if (isLoggedIn()) {
            redirect('dashboard');
        }
        
        // Handle login form submission
        if (isPostRequest()) {
            // Get form data
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            
            // Validate form data
            $errors = [];
            
            if (empty($email)) {
                $errors[] = 'Email không được để trống';
            }
            
            if (empty($password)) {
                $errors[] = 'Mật khẩu không được để trống';
            }
            
            // If no errors, proceed with login
            if (empty($errors)) {
                // Prepare SQL statement
                $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND status = 'active'");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Check if user exists
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['role'];
                        
                        // Redirect to dashboard
                        redirect('dashboard');
                    } else {
                        $errors[] = 'Email hoặc mật khẩu không đúng';
                    }
                } else {
                    $errors[] = 'Email hoặc mật khẩu không đúng';
                }
                
                $stmt->close();
            }
        }
        
        // Include login view
        include 'views/auth/login.php';
        break;
}
