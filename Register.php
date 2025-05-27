<?php
require 'config.php';
require 'social_login.php';
$reg_error = '';
$reg_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$first_name || !$last_name || !$email || !$password) {
        $reg_error = 'All fields are required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $reg_error = 'Invalid email format!';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $reg_error = 'Email already registered!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $first_name, $last_name, $email, $hash);
            if ($stmt->execute()) {
                $reg_success = 'Registration successful! <a href="login.php">Login here</a>.';
            } else {
                $reg_error = 'Registration failed. Try again.';
            }
        }
        $stmt->close();
    }
}

// Handle social login requests
if (isset($_GET['social'])) {
    switch ($_GET['social']) {
        case 'google':
            handleGoogleLogin();
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign up</title>
    <style>
        body { background: #f2f3f7; font-family: Arial, sans-serif; }
        .container { width: 700px; margin: 60px auto; display: flex; box-shadow: 0 0 20px #ccc; border-radius: 12px; overflow: hidden; background: #fff; }
        .left { background: #0d7cff; color: #fff; flex: 1.2; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px 20px; }
        .left h1 { font-size: 2.5em; margin-bottom: 10px; text-align: center; }
        .left p { margin-top: 20px; font-size: 1.1em; text-align: center; }
        .right { flex: 1.8; padding: 40px 30px; display: flex; flex-direction: column; justify-content: center; }
        .right h2 { margin-bottom: 10px; font-size: 1.5em; font-weight: bold; text-align: center; }
        .right .subtitle { text-align: center; margin-bottom: 18px; }
        .google-btn { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border: 1px solid #0d7cff; 
            color: #222; 
            background: #fff; 
            border-radius: 5px; 
            padding: 10px; 
            font-size: 1em; 
            cursor: pointer; 
            margin: 0 auto 18px auto; 
            width: 90%;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .google-btn:hover { background-color: #f5f5f5; }
        .google-btn img { width: 22px; height: 22px; margin-right: 10px; }
        .or { text-align: center; margin-bottom: 10px; color: #888; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 6px; }
        .form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1em; }
        .btn { width: 100%; background: #0d7cff; color: #fff; border: none; padding: 12px; border-radius: 5px; font-size: 1.1em; cursor: pointer; }
        .error { color: #d8000c; background: #ffd2d2; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .success { color: #4F8A10; background: #DFF2BF; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .login-link { text-align: center; margin-top: 10px; }
        .login-link a { color: #0d7cff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h1>Making<br>Every<br>Moment<br>Count —<br>Safely.</h1>
            <p>Create and account to Join Our Community</p>
        </div>
        <div class="right">
            <h2>Sign up</h2>
            <div class="subtitle">Join the community today!</div>
            <?php if ($reg_error): ?><div class="error"><?= htmlspecialchars($reg_error) ?></div><?php endif; ?>
            <?php if ($reg_success): ?><div class="success"><?= $reg_success ?></div><?php endif; ?>
            
            <a href="?social=google" class="google-btn">
                <img src="https://static.vecteezy.com/system/resources/previews/022/484/495/non_2x/google-chrome-icon-logo-symbol-free-png.png" alt="Google">
                Sign up with Google
            </a>
            
            <div class="or">or</div>
            <form method="post" action="register.php">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="e.g. 2McDc6cdN8jk9z" required>
                </div>
                <button class="btn" type="submit">Sign up</button>
            </form>
            <div class="login-link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>
</html>