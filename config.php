<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('user_auth', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'user_auth');
define('DB_PORT', '3306'); // Default MySQL port

// Attempt to connect to MySQL database
try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
    
    // Check connection
    if($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper encoding
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage() . "\nPlease make sure MySQL is running in XAMPP Control Panel.");
}

// Optional: Enable error reporting for debugging
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
?> 