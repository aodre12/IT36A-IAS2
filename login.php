<?php
require 'config.php';
require 'otp_functions.php';
require 'social_login.php';
session_start();

$login_error = '';
$otp_sent = false;
$email = '';
$role = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (isset($_POST['send_otp'])) {
        if ($email && $role) {
            if (generateOTP($email)) {
                $otp_sent = true;
                $login_error = "OTP has been sent to your email!";
            } else {
                $login_error = "Failed to send OTP. Try again.";
            }
        } else {
            $login_error = "Email and role are required!";
        }
    } elseif (isset($_POST['verify_otp'])) {
        $otp = trim($_POST['otp'] ?? '');
        if (verifyOTP($email, $otp)) {
            $stmt = $pdo->prepare("SELECT id, role FROM users WHERE email = ? AND role = ?");
            $stmt->execute([$email, $role]);
            $user = $stmt->fetch();
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                header("Location: {$role}_dashboard.php");
                exit;
            } else {
                $login_error = 'Invalid role for this email.';
            }
        } else {
            $login_error = "Invalid OTP!";
            $otp_sent = true;
        }
    } else {
        if (empty($email) || empty($password) || empty($role)) {
            $login_error = "Email, password, and role are required!";
        } else {
            $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password']) && $user['role'] === $role) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                header("Location: {$role}_dashboard.php");
                exit;
            } else {
                $login_error = "Invalid email, password, or role!";
            }
        }
    }
}

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
    <title>Combined Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-container { max-width: 400px; margin: auto; }
        .hidden { display: none; }
        .error { color: red; margin-bottom: 10px; }
        .social-buttons a {
            display: inline-block;
            padding: 10px;
            margin: 5px;
            background: #eee;
            border-radius: 5px;
            text-decoration: none;
        }
        .toggle-btns button {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>

        <?php if ($login_error): ?>
            <p class="error"><?= htmlspecialchars($login_error) ?></p>
        <?php endif; ?>

        <div class="toggle-btns">
            <button type="button" onclick="showForm('password')">Password Login</button>
            <button type="button" onclick="showForm('otp')">OTP Login</button>
        </div>

        <!-- Password Login Form -->
        <form id="passwordForm" method="post" <?= $otp_sent ? 'class="hidden"' : '' ?>>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <label>Role</label>
                <select name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="staff" <?= $role === 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            <button type="submit">Login</button>
        </form>

        <!-- OTP Login Form -->
        <form id="otpForm" method="post" <?= $otp_sent || isset($_POST['send_otp']) ? '' : 'class="hidden"' ?>>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div>
                <label>Role</label>
                <select name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="staff" <?= $role === 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>

            <?php if ($otp_sent): ?>
                <div>
                    <label>Enter OTP</label>
                    <input type="text" name="otp" required>
                </div>
                <button type="submit" name="verify_otp">Verify OTP</button>
            <?php else: ?>
                <button type="submit" name="send_otp">Send OTP</button>
            <?php endif; ?>
        </form>

        <!-- Social Login -->
        <div class="social-buttons">
            <h4>Or login with</h4>
            <a href="?social=google">Google</a>
            <a href="?social=facebook">Facebook</a>
        </div>
    </div>

    <script>
        function showForm(type) {
            document.getElementById('passwordForm').classList.add('hidden');
            document.getElementById('otpForm').classList.add('hidden');
            if (type === 'password') {
                document.getElementById('passwordForm').classList.remove('hidden');
            } else {
                document.getElementById('otpForm').classList.remove('hidden');
            }
        }
    </script>
</body>
</html>