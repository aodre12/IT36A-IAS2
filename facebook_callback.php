<?php
require_once 'social_login.php';

if (isset($_GET['code'])) {
    // Exchange code for access token
    $tokenUrl = 'https://graph.facebook.com/v12.0/oauth/access_token';
    $data = [
        'client_id' => FACEBOOK_APP_ID,
        'client_secret' => FACEBOOK_APP_SECRET,
        'redirect_uri' => FACEBOOK_REDIRECT_URI,
        'code' => $_GET['code']
    ];

    $ch = curl_init($tokenUrl . '?' . http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $token = json_decode($response, true);

    if (isset($token['access_token'])) {
        // Get user info
        $ch = curl_init('https://graph.facebook.com/me?fields=id,email,first_name,last_name&access_token=' . $token['access_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userInfo = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($userInfo['id'])) {
            $userId = handleSocialUser(
                $userInfo['id'],
                $userInfo['email'],
                $userInfo['first_name'],
                $userInfo['last_name'],
                'facebook'
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