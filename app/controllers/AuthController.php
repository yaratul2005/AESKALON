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

    // --- New AJAX API Methods ---
    
    private function jsonResponse($status, $message, $data = []) {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
        exit;
    }

    public function apiLogin() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // CAPTCHA
        if (Captcha::isEnabled() && !Captcha::verify($_POST['g-recaptcha-response'] ?? '')) {
            $this->jsonResponse('error', "CAPTCHA Check Failed.");
        }

        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE email = ?", [$email])->fetch();
        
        if (!$user) {
            $this->jsonResponse('error', "Invalid email or password.");
        }

        // If they registered with Google, hash might be empty
        if (empty($user['password_hash'])) {
            $this->jsonResponse('error', "This account was created with Google. Please continue with Google.");
        }

        if (password_verify($password, $user['password_hash'])) {
            if (!$user['is_verified']) {
                $this->jsonResponse('unverified', "Please verify your email first.");
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? 'https://ui-avatars.com/api/?name='.$user['username'].'&background=random';
            
            $this->jsonResponse('success', "Logged in successfully.");
        } else {
            $this->jsonResponse('error', "Invalid email or password.");
        }
    }

    public function apiRegister() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Optional CAPTCHA on register
        if (Captcha::isEnabled() && !Captcha::verify($_POST['g-recaptcha-response'] ?? '')) {
            $this->jsonResponse('error', "CAPTCHA Check Failed.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse('error', "Invalid email address.");
        }
        if (strlen($password) < 6) {
            $this->jsonResponse('error', "Password must be at least 6 characters.");
        }

        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE email = ?", [$email])->fetch();
        
        if ($user && $user['is_verified']) {
            $this->jsonResponse('error', "Email already registered. Please login.");
        }

        $otp = sprintf("%06d", mt_rand(1, 999999));
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        if ($user && !$user['is_verified']) {
            // Re-send OTP to existing unverified user
            $db->query("UPDATE users SET verify_token = ?, password_hash = ? WHERE email = ?", [$otp, $hash, $email]);
            $tempUsername = $user['username'];
        } else {
            // New partial user
            $tempUsername = 'user_' . substr(md5(uniqid()), 0, 8);
            $sql = "INSERT INTO users (username, email, verify_token, is_verified, password_hash) VALUES (?, ?, ?, 0, ?)";
            $db->query($sql, [$tempUsername, $email, $otp, $hash]); 
        }

        // Send OTP Email
        $emailResult = $this->sendOtpEmail($email, $otp);
        
        if (!$emailResult['success']) {
            $this->jsonResponse('error', "Email Delivery Failed: Cannot send OTP. Check SMTP provider.");
        }

        $_SESSION['verify_email'] = $email; // Store for verify step
        $this->jsonResponse('success', "OTP sent to your email.");
    }

    public function apiVerifyOtp() {
        $email = $_SESSION['verify_email'] ?? ($_POST['email'] ?? '');
        $otp = $_POST['otp'] ?? '';
        
        if (!$email || !$otp) {
            $this->jsonResponse('error', "Missing verification data.");
        }

        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE email = ? AND verify_token = ?", [$email, $otp])->fetch();
        
        if (!$user) {
            $this->jsonResponse('error', "Invalid or expired OTP code.");
        }

        // Verify user
        $db->query("UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = ?", [$user['id']]);
        
        // Log them in immediately
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? 'https://ui-avatars.com/api/?name='.$user['username'].'&background=random';
        unset($_SESSION['verify_email']);
        
        $this->jsonResponse('success', "Account verified and logged in.");
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
    private function sendOtpEmail($to, $otp) {
        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $smtp = new SMTP($s['smtp_host'], $s['smtp_port'], $s['smtp_user'], $s['smtp_pass']);
        
        $body = "<h2>Your Verification Code</h2>";
        $body .= "<p>Use the following 6-digit code to complete your registration:</p>";
        $body .= "<h1 style='color: #0ea5e9; font-size: 32px; letter-spacing: 5px;'>$otp</h1>";
        $body .= "<p>This code will expire shortly.</p>";
        
        $result = $smtp->send($to, "Your OTP Verification Code", $body, $s['smtp_from_email'], $s['site_name']);
        
        if (!$result) {
            return ['success' => false, 'error' => implode(' | ', $smtp->getLogs())];
        }
        return ['success' => true];
    }
    
    public function logout() {
        session_destroy();
        header('Location: /');
    }
}
