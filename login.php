<?php
require 'config.php';
require 'otp_functions.php';
require 'social_login.php';
$login_error = '';
$otp_sent = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_otp'])) {
        $email = trim($_POST['email'] ?? '');
        if ($email) {
            if (generateOTP($email)) {
                $login_error = "OTP has been sent to your email address!";
                $otp_sent = true;
            } else {
                $login_error = 'Failed to send OTP. Please try again.';
            }
        }
    } elseif (isset($_POST['verify_otp'])) {
        $email = trim($_POST['email'] ?? '');
        $otp = trim($_POST['otp'] ?? '');
        
        if (verifyOTP($email, $otp)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $email;
                header('Location: dashboard.php');
                exit;
            }
        } else {
            $login_error = 'Invalid OTP!';
            $otp_sent = true;
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['email'] = $email;
                header('Location: dashboard.php');
                exit;
            } else {
                $login_error = 'Invalid email or password!';
            }
        } else {
            $login_error = 'Invalid email or password!';
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
        case 'facebook':
            handleFacebookLogin();
            break;
    }
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
        .form-group input[type="email"], .form-group input[type="password"], .form-group input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1em; }
        .options { display: flex; align-items: center; margin-bottom: 18px; }
        .options input[type="checkbox"] { margin-right: 6px; }
        .options a { margin-left: 10px; color: #0d7cff; text-decoration: none; font-size: 0.98em; }
        .login-btn { width: 100%; background: #0d7cff; color: #fff; border: none; padding: 12px; border-radius: 5px; font-size: 1.1em; cursor: pointer; margin-bottom: 18px; }
        .social-login { display: flex; flex-direction: column; gap: 10px; margin-bottom: 10px; }
        .social-btn { 
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            color: #222;
        }
        .social-btn:hover { background-color: #f5f5f5; }
        .social-btn img { width: 24px; height: 24px; margin-right: 10px; }
        .or { text-align: center; margin: 15px 0; color: #888; position: relative; }
        .or::before, .or::after { content: ''; position: absolute; top: 50%; width: 45%; height: 1px; background: #ddd; }
        .or::before { left: 0; }
        .or::after { right: 0; }
        .signup-link { text-align: center; margin-top: 10px; font-size: 1em; }
        .signup-link a { color: #0d7cff; text-decoration: none; }
        .error { color: #d8000c; background: #ffd2d2; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .otp-form { display: none; }
        .otp-form.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h1>Adventure start<br>here</h1>
            <p>Create and account to Join Our Community</p>
        </div>
        <div class="right">
            <h2>Hello ! Welcome back</h2>
            <?php if ($login_error): ?>
                <div class="error"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            
            <form method="post" action="login.php" class="<?= $otp_sent ? 'otp-form active' : '' ?>">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>
                <div class="options">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember" style="margin-bottom:0;">Remember me</label>
                </div>
                <button class="login-btn" type="submit">Login</button>
            </form>

            <form method="post" action="login.php" class="otp-form <?= $otp_sent ? 'active' : '' ?>">
                <div class="form-group">
                    <label for="otp_email">Email</label>
                    <input type="email" id="otp_email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Enter your email address" required>
                </div>
                <?php if ($otp_sent): ?>
                    <div class="form-group">
                        <label for="otp">Enter OTP</label>
                        <input type="text" id="otp" name="otp" placeholder="Enter 6-digit OTP" required>
                    </div>
                    <button class="login-btn" type="submit" name="verify_otp">Verify OTP</button>
                <?php else: ?>
                    <button class="login-btn" type="submit" name="send_otp">Send OTP</button>
                <?php endif; ?>
                <div class="options">
                    <a href="#" onclick="showPasswordForm()">Back to Password Login</a>
                </div>
            </form>

            <div class="or">or</div>
            <div class="social-login">
                <a href="?social=google" class="social-btn">
                    <img src="https://static.vecteezy.com/system/resources/previews/022/484/495/non_2x/google-chrome-icon-logo-symbol-free-png.png" alt="Google">
                    Continue with Google
                </a>
                <a href="?social=facebook" class="social-btn">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Facebook_Logo_%282019%29.png" alt="Facebook">
                    Continue with Facebook
                </a>
            </div>
            <div class="signup-link">
                Don't Have an account? <a href="register.php">Create Account</a>
            </div>
        </div>
    </div>

    <script>
        function showOtpForm() {
            document.querySelectorAll('form').forEach(form => form.classList.remove('active'));
            document.querySelector('.otp-form').classList.add('active');
        }

        function showPasswordForm() {
            document.querySelectorAll('form').forEach(form => form.classList.remove('active'));
            document.querySelector('form:not(.otp-form)').classList.add('active');
        }
    </script>
</body>
</html>