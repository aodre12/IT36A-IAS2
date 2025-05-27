<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
function checkLogin() {
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }
}

// Function to set session variables after successful login
function setUserSession($user_id, $username) {
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = $user_id;
    $_SESSION["username"] = $username;
    $_SESSION['role'] = $user['role'];
}

// Function to destroy session and redirect to login page
function logout() {
    // Initialize the session
    session_start();
    
    // Unset all of the session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header("location: login.php");
    exit;
}
?> 