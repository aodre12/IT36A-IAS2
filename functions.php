<?php
// Include config file
require_once "config.php";

// Function to check if email exists
function emailExists($email) {
    global $conn;
    $sql = "SELECT id FROM users WHERE email = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) > 0) {
                return true;
            }
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to check if username exists
function usernameExists($username) {
    global $conn;
    $sql = "SELECT id FROM users WHERE username = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) > 0) {
                return true;
            }
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to register a new user
function registerUser($first_name, $last_name, $username, $email, $password) {
    global $conn;
    
    // Check if email exists
    if(emailExists($email)) {
        return "Email already exists.";
    }
    
    // Check if username exists
    if(usernameExists($username)) {
        return "Username already exists.";
    }
    
    // Prepare an insert statement
    $sql = "INSERT INTO users (first_name, last_name, username, email, password) VALUES (?, ?, ?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $username, $email, $password);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)) {
            return "success";
        } else {
            return "Something went wrong. Please try again later.";
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    return "Something went wrong. Please try again later.";
}

// Function to verify login
function verifyLogin($email, $password) {
    global $conn;
    
    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                if(mysqli_stmt_fetch($stmt)) {
                    if(password_verify($password, $hashed_password)) {
                        return array("success" => true, "user_id" => $id, "username" => $username);
                    }
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    return array("success" => false, "message" => "Invalid email or password.");
}

// Function to get user data
function getUserData($user_id) {
    global $conn;
    
    $sql = "SELECT first_name, last_name, username, email FROM users WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if($row = mysqli_fetch_assoc($result)) {
                return $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    return false;
}
?> 