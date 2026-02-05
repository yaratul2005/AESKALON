<?php

require_once '../core/SMTP.php';
require_once '../core/Database.php';
require_once '../core/GoogleAuth.php';
require_once '../core/Captcha.php';

class AuthController {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // --- Views ---
    public function login() {
        if (isset($_SESSION['user_id'])) exit(header('Location: /dashboard'));
        require_once '../app/views/auth/login.php';
    }

    public function register() {
        if (isset($_SESSION['user_id'])) exit(header('Location: /dashboard'));
        require_once '../app/views/auth/register.php';
    }

    // --- Logic ---
    
    // 1. Email Registration (Step 1: Send Verify Link)
    public function doRegister() {
        $email = $_POST['email'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email address.";
            header('Location: /register');
            return;
        }

        $db = Database::getInstance();
        
        // Check if exists
        $user = $db->query("SELECT * FROM users WHERE email = ?", [$email])->fetch();
        if ($user) {
            $_SESSION['error'] = "Email already registered. Please login.";
            header('Location: /login');
            return;
        }

        // Create Verify Token
        $token = bin2hex(random_bytes(32));
        
        // Insert partial user
        $tempUsername = 'user_' . substr(md5(uniqid()), 0, 8);
        
        $sql = "INSERT INTO users (username, email, verify_token, is_verified, password_hash) VALUES (?, ?, ?, 0, '')";
        $db->query($sql, [$tempUsername, $email, $token]); 

        // Send Email
        $this->sendVerificationEmail($email, $token);

        $_SESSION['success'] = "Verification link sent! Please check your email.";
        header('Location: /login');
    }

    public function doLogin() {
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
        
        if ($user && password_verify($password, $user['password_hash'])) {
            if (!$user['is_verified']) {
                $_SESSION['error'] = "Please verify your email first.";
                header('Location: /login');
                return;
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? 'https://ui-avatars.com/api/?name='.$user['username'].'&background=random';
            
            header('Location: /dashboard');
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header('Location: /login');
        }
    }

    // 2. Verify Link Clicked -> Set Password Form
    public function verify($token) {
        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE verify_token = ?", [$token])->fetch();
        
        if (!$user) {
            die("Invalid or expired token.");
        }

        // Show Set Password View
        require_once '../app/views/auth/set_password.php';
    }

    // 3. Complete Registration (Set Password)
    public function completeRegistration() {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (strlen($password) < 6) {
            die("Password must be at least 6 characters.");
        }

        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE verify_token = ?", [$token])->fetch();
        
        if (!$user) {
            die("Invalid token.");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $db->query("UPDATE users SET password_hash = ?, is_verified = 1, verify_token = NULL WHERE id = ?", [$hash, $user['id']]);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? 'https://ui-avatars.com/api/?name='.$user['username'].'&background=random';
        
        header('Location: /dashboard');
    }

    // --- Google Auth ---
    public function google() {
        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Dynamic Redirect URI
        $redirectUri = $s['google_redirect_uri'] ?? '';
        if (empty($redirectUri)) {
             $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
             $redirectUri = $protocol . $_SERVER['HTTP_HOST'] . '/auth/google/callback';
        }

        $g = new GoogleAuth($s['google_client_id'], $s['google_client_secret'], $redirectUri);
        header('Location: ' . $g->getAuthUrl());
    }

    public function googleCallback() {
        $code = $_GET['code'] ?? '';
        if (!$code) header('Location: /login');

        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $redirectUri = $s['google_redirect_uri'] ?? '';
        if (empty($redirectUri)) {
             $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
             $redirectUri = $protocol . $_SERVER['HTTP_HOST'] . '/auth/google/callback';
        }

        $g = new GoogleAuth($s['google_client_id'], $s['google_client_secret'], $redirectUri);
        $token = $g->getToken($code);
        
        if (isset($token['error'])) die("Google Login Failed: " . json_encode($token));
        
        $info = $g->getUserInfo($token['access_token']);
        if (!$info) die("Failed to get Google User Info.");

        $email = $info['email'];
        $googleId = $info['id'];
        $avatar = $info['picture'] ?? '';
        
        // Check User
        $user = $db->query("SELECT * FROM users WHERE email = ?", [$email])->fetch();
        
        if ($user) {
            if (!$user['google_id']) {
                $db->query("UPDATE users SET google_id = ?, avatar = COALESCE(avatar, ?) WHERE id = ?", [$googleId, $avatar, $user['id']]);
                $user['avatar'] = $user['avatar'] ?: $avatar;
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? 'https://ui-avatars.com/api/?name='.$user['username'].'&background=random';
        } else {
            $username = 'user_' . substr(md5(uniqid()), 0, 8);
            $db->query("INSERT INTO users (username, email, google_id, avatar, is_verified, password_hash) VALUES (?, ?, ?, ?, 1, '')", 
                [$username, $email, $googleId, $avatar]);
            
            $id = $db->getPdo()->lastInsertId();
            $_SESSION['user_id'] = $id;
            $_SESSION['user_username'] = $username;
            $_SESSION['user_avatar'] = $avatar;
        }
        
        header('Location: /dashboard');
    }

    // --- Helpers ---
    private function sendVerificationEmail($to, $token) {
        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $smtp = new SMTP($s['smtp_host'], $s['smtp_port'], $s['smtp_user'], $s['smtp_pass']);
        
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $link = $protocol . $_SERVER['HTTP_HOST'] . "/verify/$token";

        $body = "<h2>Welcome!</h2>";
        $body .= "<p>Click the link below to verify your account and set your password:</p>";
        $body .= "<a href='$link'>$link</a>";
        
        $smtp->send($to, "Verify your Account", $body, $s['smtp_from_email'], $s['site_name']);
    }
    
    public function logout() {
        session_destroy();
        header('Location: /');
    }
}
