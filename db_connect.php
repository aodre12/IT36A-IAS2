<?php
$servername = "localhost"; // Or your database server address
$username = "root"; // Your database username
$password = ""; // Your database password (leave empty if none)
$dbname = "user_auth"; // The database name we created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // Optional: uncomment for testing
?> 