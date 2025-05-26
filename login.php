<?php
require 'config.php';
session_start();
$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Fetch id, hashed password, and role from the database
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hash, $role);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            
            // Redirect based on the user's role
            if ($role === 'admin') {
                header('Location: admin.php');
                exit;
            } elseif ($role === 'employee') {
                header('Location: employee.php');
                exit;
            } else {
                header('Location: dashboard.php');
                exit;
            }
        } else {
            $login_error = 'Invalid email or password!';
        }
    } else {
        $login_error = 'Invalid email or password!';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { background: #f2f3f7; font-family: Arial, sans-serif; }
        .container { width: 700px; margin: 60px auto; display: flex; box-shadow: 0 0 20px #ccc; border-radius: 12px; overflow: hidden; background: #fff; }
        .left { background: #0d7cff; color: #fff; flex: 1.2; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px 20px; }
        .left h1 { font-size: 2.5em; margin-bottom: 10px; }
        .left p { margin-top: 20px; font-size: 1.1em; }
        .right { flex: 1.8; padding: 40px 30px; display: flex; flex-direction: column; justify-content: center; }
        .right h2 { margin-bottom: 20px; font-size: 1.5em; font-weight: bold; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 6px; }
        .form-group input[type="email"], .form-group input[type="password"] {
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            font-size: 1em;
        }
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        .options { display: flex; align-items: center; margin-bottom: 18px; }
        .options input[type="checkbox"] { margin-right: 6px; }
        .options a { margin-left: 10px; color: #0d7cff; text-decoration: none; font-size: 0.98em; }
        .login-btn { width: 100%; background: #0d7cff; color: #fff; border: none; padding: 12px; border-radius: 5px; font-size: 1.1em; cursor: pointer; margin-bottom: 18px; }
        .social-login { display: flex; align-items: center; justify-content: center; margin-bottom: 10px; }
        .social-login img { width: 32px; height: 32px; margin: 0 8px; }
        .or { text-align: center; margin-bottom: 10px; color: #888; }
        .signup-link { text-align: center; margin-top: 10px; font-size: 1em; }
        .signup-link a { color: #0d7cff; text-decoration: none; }
        .error { color: #d8000c; background: #ffd2d2; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h1>Adventure starts<br>here</h1>
            <p>Create an account to Join Our Community</p>
        </div>
        <div class="right">
            <h2>Hello! Welcome back</h2>
            <?php if ($login_error): ?>
                <div class="error"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>
                <div class="options">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember" style="margin-bottom:0;">Remember me</label>
                    <a href="#">Reset Password!</a>
                </div>
                <button class="login-btn" type="submit">Login</button>
            </form>
            <div class="or">or</div>
            <div class="social-login">
                <img src="https://cdn4.iconfinder.com/data/icons/logos-brands-7/512/google_logo-google_icongoogle-1024.png" alt="Google">
                <img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Facebook_Logo_%282019%29.png" alt="Facebook">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg" alt="Apple">
            </div>
            <div class="signup-link">
                Don't Have an account? <a href="register.php">Create Account</a>
            </div>
        </div>
    </div>
</body>
</html>