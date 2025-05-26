<?php
require 'db.php';
$reg_error = '';
$reg_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$email || !$password || !$confirm) {
        $reg_error = 'All fields are required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $reg_error = 'Invalid email format!';
    } elseif ($password !== $confirm) {
        $reg_error = 'Passwords do not match!';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $reg_error = 'Email already registered!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $hash);
            if ($stmt->execute()) {
                $reg_success = 'Registration successful! <a href="login.php">Login here</a>.';
            } else {
                $reg_error = 'Registration failed. Try again.';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body { background: #f2f3f7; font-family: Arial, sans-serif; }
        .container { width: 400px; margin: 60px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px #ccc; }
        h2 { text-align: center; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1em; }
        .btn { width: 100%; background: #0d7cff; color: #fff; border: none; padding: 12px; border-radius: 5px; font-size: 1.1em; cursor: pointer; }
        .error { color: #d8000c; background: #ffd2d2; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .success { color: #4F8A10; background: #DFF2BF; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .login-link { text-align: center; margin-top: 10px; }
        .login-link a { color: #0d7cff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create Account</h2>
        <?php if ($reg_error): ?><div class="error"><?= htmlspecialchars($reg_error) ?></div><?php endif; ?>
        <?php if ($reg_success): ?><div class="success"><?= $reg_success ?></div><?php endif; ?>
        <form method="post" action="register.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm">Confirm Password</label>
                <input type="password" id="confirm" name="confirm" required>
            </div>
            <button class="btn" type="submit">Register</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>