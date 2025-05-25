<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'user_auth');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper encoding
$conn->set_charset("utf8mb4");

// Optional: Enable error reporting for debugging
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
?> 