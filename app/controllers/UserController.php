<?php

class UserController {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function dashboard() {
        if (!isset($_SESSION['user_id'])) exit(header('Location: /login'));
        
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        // Fetch User Info
        $user = $db->query("SELECT * FROM users WHERE id = ?", [$userId])->fetch();

        if (!$user) {
            // Invalid session or deleted user
            session_destroy();
            header('Location: /login');
            exit;
        }
        
        // Fetch Watch History
        $history = $db->query(
            "SELECT h.*, COALESCE(h.watched_at, NOW()) as date 
             FROM user_history h 
             WHERE user_id = ? 
             ORDER BY watched_at DESC LIMIT 20", 
            [$userId]
        )->fetchAll();

        // Fetch Watch Later
        $watchLater = $db->query(
            "SELECT w.*, w.added_at as date 
             FROM watch_later w 
             WHERE user_id = ? 
             ORDER BY added_at DESC", 
            [$userId]
        )->fetchAll();

        // Fetch Settings for Layout
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

        $pageTitle = "My Dashboard";
        
        ob_start();
        require_once '../app/views/user/dashboard.php';
        $content = ob_get_clean();

        require_once '../app/views/layout.php';
    }

    public function settings() {
        if (!isset($_SESSION['user_id'])) exit(header('Location: /login'));
        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();
        $pageTitle = "Account Settings";
        require_once '../app/views/user/settings.php';
    }

    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) exit(header('Location: /login'));
        require_once '../core/Csrf.php';
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
             $_SESSION['error'] = "Security check failed. Try again.";
             header('Location: /settings');
             return;
        }
        $userId = $_SESSION['user_id'];
        $db = Database::getInstance();
        
        // 1. Password Update
        if (!empty($_POST['new_password'])) {
            $current = $_POST['current_password'] ?? '';
            $user = $db->query("SELECT password_hash FROM users WHERE id = ?", [$userId])->fetch();
            
            if (password_verify($current, $user['password_hash'])) {
                if (strlen($_POST['new_password']) >= 6) {
                    $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    $db->query("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $userId]);
                    $_SESSION['success'] = "Password updated.";
                } else {
                    $_SESSION['error'] = "New password must be at least 6 characters.";
                }
            } else {
                $_SESSION['error'] = "Incorrect current password.";
            }
        }
        
        // 2. Info Update (Email/Username/Bio)
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $bio = $_POST['bio'] ?? '';
        
        if ($username && $email) {
            try {
                $db->query("UPDATE users SET username=?, email=?, bio=? WHERE id=?", [$username, $email, $bio, $userId]);
                $_SESSION['user_username'] = $username;
                $_SESSION['success'] = "Profile updated successfully.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Username or Email already taken.";
            }
        }
        
        // 3. Avatar Upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $uploadDir = '../public/assets/avatars/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $filename = 'user_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $filename)) {
                    $publicPath = '/assets/avatars/' . $filename;
                    $db->query("UPDATE users SET avatar = ? WHERE id = ?", [$publicPath, $userId]);
                    $_SESSION['user_avatar'] = $publicPath;
                }
            } else {
                $_SESSION['error'] = "Invalid image format. Allowed: JPG, PNG, WEBP.";
            }
        }

        header('Location: /settings');
    }

    public function deleteAccount() {
        if (!isset($_SESSION['user_id'])) exit(header('Location: /login'));
        require_once '../core/Csrf.php';
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
             $_SESSION['error'] = "Security check failed.";
             header('Location: /settings');
             return;
        }
        $userId = $_SESSION['user_id'];
        
        if (isset($_POST['confirm_delete'])) {
            $db = Database::getInstance();
            $db->query("DELETE FROM users WHERE id = ?", [$userId]);
            session_destroy();
            header('Location: /');
            exit;
        }
        header('Location: /settings');
    }

    // --- API Methods ---
    public function toggleWatchLater() {
        if (!isset($_SESSION['user_id'])) exit(json_encode(['error' => 'Login required']));
        
        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents('php://input'), true);
        $tmdbId = $data['id'] ?? 0;
        $type = $data['type'] ?? 'movie';

        $db = Database::getInstance();
        $exists = $db->query("SELECT id FROM watch_later WHERE user_id=? AND tmdb_id=? AND type=?", [$userId, $tmdbId, $type])->fetch();

        if ($exists) {
            $db->query("DELETE FROM watch_later WHERE id=?", [$exists['id']]);
            echo json_encode(['status' => 'removed']);
        } else {
            $db->query("INSERT INTO watch_later (user_id, tmdb_id, type) VALUES (?, ?, ?)", [$userId, $tmdbId, $type]);
            echo json_encode(['status' => 'added']);
        }
    }

    public function addToHistory() {
        if (!isset($_SESSION['user_id'])) return;
        
        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents('php://input'), true);
        $tmdbId = $data['id'] ?? 0;
        $type = $data['type'] ?? 'movie';

        $db = Database::getInstance();
        // Insert or Update timestamp
        $db->query("INSERT INTO user_history (user_id, tmdb_id, type) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE watched_at=NOW()", [$userId, $tmdbId, $type]);
        echo json_encode(['status' => 'ok']);
    }
}
