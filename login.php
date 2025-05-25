<?php
// Include config file
require_once "config.php";
require_once "functions.php";
require_once "session.php";

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if email is empty
    if(empty(trim($_POST["email"]))) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)) {
        $result = verifyLogin($email, $password);
        
        if($result["success"]) {
            // Password is correct, start a new session
            setUserSession($result["user_id"], $result["username"]);
            
            // Redirect user to welcome page
            header("location: welcome.php");
        } else {
            $login_err = $result["message"];
        }
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
            <h2>Adventure start here</h2>
            <p>Create and account to Join Our Community</p>
        </div>
        <div class="right-panel">
            <h2>Hello ! Welcome back</h2>
            <?php 
            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" placeholder="Enter your email address">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>    
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="********">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group form-options">
                    <input type="checkbox" name="remember" id="remember"> <label for="remember">Remember me</label>
                    <a href="#" class="reset-password">Reset Password!</a>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Login</button>
                </div>
            </form>
            <div class="social-login">
                <p>or</p>
                <div class="social-icons">
                    <img src="google_icon.png" alt="Google">
                    <img src="facebook_icon.png" alt="Facebook">
                    <img src="apple_icon.png" alt="Apple">
                </div>
            </div>
            <p class="create-account">Don't have an account? <a href="signup.php">Create Account</a></p>
        </div>
    </div>
</body>
</html> 