<?php
require_once 'config.php';
session_start();

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', 'http://localhost/IT36A-IAS2/google_callback.php');

// Facebook OAuth Configuration
define('FACEBOOK_APP_ID', 'YOUR_FACEBOOK_APP_ID');
define('FACEBOOK_APP_SECRET', 'YOUR_FACEBOOK_APP_SECRET');
define('FACEBOOK_REDIRECT_URI', 'http://localhost/IT36A-IAS2/facebook_callback.php');

function handleGoogleLogin() {
    $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'email profile',
        'access_type' => 'online',
        'prompt' => 'select_account'
    ]);
    
    header('Location: ' . $authUrl);
    exit;
}

function handleFacebookLogin() {
    $authUrl = 'https://www.facebook.com/v12.0/dialog/oauth?' . http_build_query([
        'client_id' => FACEBOOK_APP_ID,
        'redirect_uri' => FACEBOOK_REDIRECT_URI,
        'state' => bin2hex(random_bytes(16)),
        'scope' => 'email,public_profile'
    ]);
    
    header('Location: ' . $authUrl);
    exit;
}

function handleSocialUser($socialId, $email, $firstName, $lastName, $socialType) {
    global $conn;
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR (social_id = ? AND social_type = ?)");
    $stmt->bind_param("sss", $email, $socialId, $socialType);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // User exists, update social login info
        $stmt = $conn->prepare("UPDATE users SET social_id = ?, social_type = ?, is_verified = 1 WHERE id = ?");
        $stmt->bind_param("ssi", $socialId, $socialType, $row['id']);
        $stmt->execute();
        return $row['id'];
    } else {
        // Create new user
        $username = strtolower($firstName . $lastName . rand(100, 999));
        $password = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password, social_id, social_type, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sssssss", $firstName, $lastName, $username, $email, $password, $socialId, $socialType);
        
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
    }
    
    return false;
}
?> 