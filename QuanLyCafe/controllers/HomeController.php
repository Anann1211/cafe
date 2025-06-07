<?php
/**
 * Home Controller
 * 
 * Handles home page functionality
 */

// Check if user is logged in
if (isLoggedIn()) {
    // Redirect to dashboard
    redirect('dashboard');
} else {
    // Redirect to login page
    redirect('login');
}
