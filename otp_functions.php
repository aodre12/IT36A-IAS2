<?php
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function generateOTP($email) {
    global $conn;
    
    // Generate a 6-digit OTP
    $otp = sprintf("%06d", mt_rand(0, 999999));
    
    // Set OTP expiry to 10 minutes from now
    $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Update user's OTP in database
    $stmt = $conn->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE email = ?");
    $stmt->bind_param("sss", $otp, $expiry, $email);
    
    if ($stmt->execute()) {
        // Send OTP via email
        if (sendOTPEmail($email, $otp)) {
            return true;
        }
    }
    
    return false;
}

function verifyOTP($email, $otp) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['otp'] === $otp && strtotime($row['otp_expiry']) > time()) {
            // Clear OTP after successful verification
            $stmt = $conn->prepare("UPDATE users SET otp = NULL, otp_expiry = NULL, is_verified = 1 WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            return true;
        }
    }
    
    return false;
}

function sendOTPEmail($email, $otp) {
    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Replace with your email
        $mail->Password = 'your-app-password'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your-email@gmail.com', 'Your Name');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #0d7cff;'>Your OTP Code</h2>
                <p>Your One-Time Password (OTP) is:</p>
                <div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px; text-align: center; font-size: 24px; letter-spacing: 5px; margin: 20px 0;'>
                    <strong>{$otp}</strong>
                </div>
                <p>This OTP will expire in 10 minutes.</p>
                <p>If you didn't request this OTP, please ignore this email.</p>
                <hr style='border: 1px solid #eee; margin: 20px 0;'>
                <p style='color: #666; font-size: 12px;'>This is an automated message, please do not reply.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?> 