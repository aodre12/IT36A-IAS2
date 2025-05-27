<?php
require_once 'social_login.php';

if (isset($_GET['code'])) {
    // Exchange code for access token
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $data = [
        'code' => $_GET['code'],
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_POST, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $token = json_decode($response, true);

    if (isset($token['access_token'])) {
        // Get user info
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token['access_token']]);
        $userInfo = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($userInfo['id'])) {
            $userId = handleSocialUser(
                $userInfo['id'],
                $userInfo['email'],
                $userInfo['given_name'],
                $userInfo['family_name'],
                'google'
            );

            if ($userId) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['email'] = $userInfo['email'];
                header('Location: dashboard.php');
                exit;
            }
        }
    }
}

// If we get here, something went wrong
header('Location: login.php?error=social_login_failed');
exit;
?> 