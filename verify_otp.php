<?php
require 'otp_functions.php';
session_start();

if (!isset($_SESSION['pending_email'], $_SESSION['pending_social'])) {
    header('Location: register.php');
    exit;
}

$email = $_SESSION['pending_email'];
$social = $_SESSION['pending_social'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    if (verifyOTP($email, $otp)) {
        // Now create/log in the user using $social info
        require 'social_login.php';
        $userId = handleSocialUser($social['id'], $email, $social['first_name'], $social['last_name'], $social['social_type']);
        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['email'] = $email;
            unset($_SESSION['pending_email'], $_SESSION['pending_social']);
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Failed to create account.';
        }
    } else {
        $error = 'Invalid OTP!';
    }
}
?>
<!-- Simple HTML form for OTP input -->
<form method="post">
    <label>Enter OTP sent to <?= htmlspecialchars($email) ?>:</label>
    <input type="text" name="otp" required>
    <button type="submit">Verify</button>
    <?php if ($error): ?><div><?= htmlspecialchars($error) ?></div><?php endif; ?>
</form>