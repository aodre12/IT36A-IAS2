<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect value of input field
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation (you would do more thorough validation in a real application)
    if (!empty($email) && !empty($password)) {
        echo "<h2>Sign Up Attempt:</h2>";
        echo "<p>Email: " . htmlspecialchars($email) . "</p>";
        echo "<p>Password: " . htmlspecialchars($password) . "</p>\n";
        // In a real application, you would process the registration here.
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
            <h2>Making Every Moment Count — Safely.</h2>
            <p>Create and account to Join Our Community</p>
        </div>
        <div class="right-panel">
            <h2>Sign up</h2>
            <p>Join the community today!</p>
            <button class="google-signin-button"><img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google" width="30"> Sign up with Google</button>
            <div class="options">or</div>
            <form action="signup.php" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="uiuxsaeed@gmail.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="e.g. 2McDc6cdN8jk9z">
                </div>
                <button type="submit">Sign up</button>
            </form>
             <div class="create-account">
                    Already Have an account? <a href="login.php">Login</a>
                </div>
        </div>
    </div>
</body>
</html> 