<?php

require_once '../core/SMTP.php';
require_once '../core/Database.php';
require_once '../core/GoogleAuth.php';
require_once '../core/Captcha.php';

class AuthController {
    
    // ... (Existing register/google methods) ...
    public function register() { require_once '../app/views/auth/register.php'; }
    public function login() { require_once '../app/views/auth/login.php'; }
    public function logout() { session_destroy(); header('Location: /'); }

    public function doLogin() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // CAPTCHA
        if (Captcha::isEnabled() && !Captcha::verify($_POST['g-recaptcha-response'] ?? '')) {
            $_SESSION['error'] = "CAPTCHA Check Failed. Are you human?";
            header('Location: /login');
            return;
        }

        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE email = ?", [$email])->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_avatar'] = $user['avatar'];
            header('Location: /dashboard');
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header('Location: /login');
        }
    }

    // ... (Keep existing methods) ...
    
    public function doRegister() {
       // ... existing registration logic ...
    }
    
    public function google() {
        $client = GoogleAuth::getClient();
        header('Location: ' . $client->createAuthUrl());
    }

    public function googleCallback() {
        // ... (existing callback) ...
        $client = GoogleAuth::getClient();
        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token);
            $oauth = new Google_Service_Oauth2($client);
            $googleUser = $oauth->userinfo->get();
            
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM users WHERE google_id = ? OR email = ?");
            $stmt->execute([$googleUser->id, $googleUser->email]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_username'] = $user['username'];
                 $_SESSION['user_avatar'] = $user['avatar'];
                header('Location: /dashboard');
            } else {
                // Register new Google User
                 $username = explode('@', $googleUser->email)[0];
                 $db->query("INSERT INTO users (username, email, google_id, avatar, is_verified) VALUES (?, ?, ?, ?, 1)", 
                    [$username, $googleUser->email, $googleUser->id, $googleUser->picture]);
                 $uid = $db->query("SELECT LAST_INSERT_ID()")->fetchColumn();
                 $_SESSION['user_id'] = $uid;
                 $_SESSION['user_username'] = $username;
                 $_SESSION['user_avatar'] = $googleUser->picture;
                 header('Location: /dashboard');
            }
        }
    }
}
