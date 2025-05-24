<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect value of input field
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        echo "<h2>Login Attempt:</h2>";
        echo "<p>Email: " . htmlspecialchars($email) . "</p>";
        echo "<p>Password: " . htmlspecialchars($password) . "</p>";
        // In a real application, you would perform validation and authentication here.
    } else {
        echo "<p>Please enter both email and password.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <h2>Making Every Moment Count — Safely.</h2>
            <p>Create and account to Join Our Community</p>
        </div>
        <div class="right-panel">
            <h2>Hello SafeTime User, Welcome !</h2>
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********">
                </div>
                <div class="options">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#">Reset Password!</a>
                </div>
                <button type="submit">Login</button>
                <div class="options">or</div>
                <div class="social-login">
                    <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google" width="30">
                    <img src="https://img.icons8.com/color/48/000000/facebook-new.png" alt="Facebook" width="30">
                    <img src="https://img.icons8.com/ios-filled/50/000000/mac-os.png" alt="Apple" width="30">

                </div>
                <div class="create-account">
                    Don't Have an account? <a href="signup.php">Create Account</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 