<?php

class UserController {
    
    public function dashboard() {
        if (!isset($_SESSION['user_id'])) exit(header('Location: /login'));
        
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        // Fetch User Info
        $user = $db->query("SELECT * FROM users WHERE id = ?", [$userId])->fetch();
        
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

        // Pass data to view
        // Note: TMDB details (images/titles) are not stored locally, so we need to fetch them client-side or cache them.
        // For performance, we'll store basic metadata in history/watch_later tables in a real app, 
        // OR we can fetch them via JS on the dashboard.
        // Given the constraints, I'll update the 'v3.0.0' SQL to include title/poster in these tables to avoid 20 API calls.
        // Wait, I can't easily change the SQL already provided unless I provide a v3.1 update.
        // Actually, the user asked for "easy login system", and "dashboard". 
        // I will implement a JS-based fetcher for the dashboard items to keep it light.

        $pageTitle = "My Dashboard";
        
        ob_start();
        require_once '../app/views/user/dashboard.php';
        $content = ob_get_clean();

        require_once '../app/views/layout.php';
    }

    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) exit(header('Location: /login'));
        
        $userId = $_SESSION['user_id'];
        $pass = $_POST['password'] ?? '';
        $avatar = $_POST['avatar'] ?? ''; // simplified avatar url input or upload
        // If file upload:
        // For now, let's keep it simple: "A random username will be given", user edits profile.
        
        $db = Database::getInstance();

        if (!empty($pass)) {
             if (strlen($pass) < 6) {
                 $_SESSION['error'] = "Password too short.";
                 header('Location: /dashboard');
                 return;
             }
             $hash = password_hash($pass, PASSWORD_DEFAULT);
             $db->query("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $userId]);
        }
        
        // Avatar Upload Logic could go here. For now, we use the UI Avatars or Google Avatar.
        // Let's allow updating the username if they want.
        $newUsername = $_POST['username'] ?? '';
        if ($newUsername) {
             $db->query("UPDATE users SET username = ? WHERE id = ?", [$newUsername, $userId]);
             $_SESSION['user_username'] = $newUsername;
        }

        $_SESSION['success'] = "Profile updated.";
        header('Location: /dashboard');
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
