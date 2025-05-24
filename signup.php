<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect value of input field
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation (you would do more thorough validation in a real application)
    if (!empty($first_name) && !empty($last_name) && !empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            echo "<h2>Sign Up Attempt:</h2>";
            echo "<p>First Name: " . htmlspecialchars($first_name) . "</p>";
            echo "<p>Last Name: " . htmlspecialchars($last_name) . "</p>";
            echo "<p>Username: " . htmlspecialchars($username) . "</p>";
            echo "<p>Email: " . htmlspecialchars($email) . "</p>";
            echo "<p>Password: " . htmlspecialchars($password) . "</p>";
            // In a real application, you would process the registration here.
        } else {
            echo "<p>Password and Confirm Password do not match.</p>";
        }
    } else {
        echo "<p>Please fill in all fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <h2>Advanture start here</h2>
            <p>Create and account to Join Our Community</p>
        </div>
        <div class="right-panel">
            <div class="logo">
                <img src="logo.png" alt="Logo">
            </div>
            <h2>Sign-up</h2>
            <p>Register yourself to do something on Lidia.</p>
            <form action="signup.php" method="post">
                <div class="form-group">
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="e.g. Alex">
                </div>
                <div class="form-group">
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="e.g. John">
                </div>
                 <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="e.g. Alex123">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="uiuxsaeed@gmail.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="e.g. 2McDc6cdN8jk9z">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="e.g. 2McDc6cdN8jk9z">
                </div>
                <button type="submit">Sign-up</button>
            </form>
             <div class="create-account">
                    Already Have an account? <a href="login.php">Login</a>
                </div>
        </div>
    </div>
</body>
</html> 