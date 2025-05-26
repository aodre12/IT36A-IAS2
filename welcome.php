<?php
// Include session file
require_once "session.php";

// Check if the user is logged in
checkLogin();

// Get user data
require_once "functions.php";
$user_data = getUserData($_SESSION["id"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="welcome-container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
            <div class="user-info">
                <h2>Your Profile Information:</h2>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($user_data["first_name"]); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user_data["last_name"]); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data["email"]); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($user_data["gender"]); ?></p>
            </div>
            <div class="actions">
                <a href="logout.php" class="btn btn-danger">Sign Out</a>
            </div>
        </div>
    </div>
</body>
</html> 