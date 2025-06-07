<?php
/**
 * Logout Controller
 * 
 * Handles user logout
 */

// Destroy session
session_destroy();

// Redirect to login page
redirect('login');
