<?php

require_once '../core/Database.php';
require_once '../core/GoogleAuth.php';
require_once '../core/FacebookAuth.php';

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

    // --- Google Auth ---
    public function google() {
        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $redirectUri = $s['google_redirect_uri'] ?? '';
        if (empty($redirectUri)) {
             $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
             $redirectUri = $protocol . $_SERVER['HTTP_HOST'] . '/auth/google/callback';
        }

        $g = new GoogleAuth($s['google_client_id'], $s['google_client_secret'], $redirectUri);
        header('Location: ' . $g->getAuthUrl());
    }

    public function googleCallback() {
        $code = $_GET['code'] ?? '';
        if (!$code) exit(header('Location: /login'));

        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $redirectUri = $s['google_redirect_uri'] ?? '';
        if (empty($redirectUri)) {
             $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
             $redirectUri = $protocol . $_SERVER['HTTP_HOST'] . '/auth/google/callback';
        }

        $g = new GoogleAuth($s['google_client_id'], $s['google_client_secret'], $redirectUri);
        $token = $g->getToken($code);
        
        if (isset($token['error'])) die("Google Login Failed: " . json_encode($token));
        
        $info = $g->getUserInfo($token['access_token']);
        if (!$info) die("Failed to get Google User Info.");

        $this->processSocialLogin('google_id', $info['id'], $info['email'], $info['picture'] ?? '');
    }

    // --- Facebook Auth ---
    public function facebook() {
        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $redirectUri = $s['facebook_redirect_uri'] ?? '';
        if (empty($redirectUri)) {
             $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
             $redirectUri = $protocol . $_SERVER['HTTP_HOST'] . '/auth/facebook/callback';
        }

        $fb = new FacebookAuth($s['facebook_app_id'], $s['facebook_app_secret'], $redirectUri);
        header('Location: ' . $fb->getAuthUrl());
    }

    public function facebookCallback() {
        $code = $_GET['code'] ?? '';
        if (!$code) exit(header('Location: /login'));

        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $redirectUri = $s['facebook_redirect_uri'] ?? '';
        if (empty($redirectUri)) {
             $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
             $redirectUri = $protocol . $_SERVER['HTTP_HOST'] . '/auth/facebook/callback';
        }

        $fb = new FacebookAuth($s['facebook_app_id'], $s['facebook_app_secret'], $redirectUri);
        $token = $fb->getToken($code);
        
        if (isset($token['error'])) {
            $_SESSION['error'] = "Facebook Login Failed. Please check App configs.";
            exit(header('Location: /login'));
        }
        
        $info = $fb->getUserInfo($token['access_token']);
        if (!$info || empty($info['email'])) {
            $_SESSION['error'] = "Facebook Login Failed: No email address provided by Facebook.";
            exit(header('Location: /login'));
        }

        $this->processSocialLogin('facebook_id', $info['id'], $info['email'], $info['picture'] ?? '');
    }

    // --- Unified Social Processing ---
    private function processSocialLogin($providerColumn, $providerId, $email, $avatar) {
        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE email = ?", [$email])->fetch();
        
        if ($user) {
            // Update OAuth ID if not set, and sync avatar if missing
            if (empty($user[$providerColumn])) {
                $db->query("UPDATE users SET $providerColumn = ?, avatar = COALESCE(avatar, ?) WHERE id = ?", [$providerId, $avatar, $user['id']]);
                $user['avatar'] = $user['avatar'] ?: $avatar;
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? 'https://ui-avatars.com/api/?name='.$user['username'].'&background=random';
        } else {
            $username = 'user_' . substr(md5(uniqid()), 0, 8);
            
            // Generate dummy password since email/password login is dropped
            $dummyHash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, email, $providerColumn, avatar, is_verified, password_hash) VALUES (?, ?, ?, ?, 1, ?)";
            $db->query($sql, [$username, $email, $providerId, $avatar, $dummyHash]);
            
            $id = $db->getPdo()->lastInsertId();
            $_SESSION['user_id'] = $id;
            $_SESSION['user_username'] = $username;
            $_SESSION['user_avatar'] = $avatar;
        }
        
        header('Location: /dashboard');
    }

    public function logout() {
        session_destroy();
        header('Location: /');
    }
}
