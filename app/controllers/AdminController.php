<?php

require_once '../core/SMTP.php';

class AdminController {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function isAuthenticated() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    // --- Auth ---
    public function login() {
        if ($this->isAuthenticated()) {
            header('Location: /admin/dashboard');
            exit;
        }
        require_once '../app/views/admin/login.php';
    }

    public function auth() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM users WHERE username = ?", [$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: /admin/dashboard');
        } else {
            $_SESSION['error'] = "Invalid credentials";
            header('Location: /admin');
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /admin');
    }

    // --- Dashboard ---
    public function dashboard() {
        if (!$this->isAuthenticated()) exit(header('Location: /admin'));
        $pageTitle = "Dashboard";
        
        // Basic Stats
        $db = Database::getInstance();
        $updateFiles = glob('../updates/*.sql');
        $stmtVer = $db->query("SELECT version FROM app_version");
        $appliedVersions = $stmtVer->fetchAll(PDO::FETCH_COLUMN);
        
        $pendingUpdatesCount = 0;
        foreach ($updateFiles as $file) {
            if (!in_array(basename($file), $appliedVersions)) $pendingUpdatesCount++;
        }

        require_once '../app/views/admin/layout.php';
    }

    // --- Users & Bans ---
    public function users() {
        if (!$this->isAuthenticated()) exit(header('Location: /admin'));
        $pageTitle = "User Management";
        
        $db = Database::getInstance();
        $bans = $db->query("SELECT * FROM ip_bans ORDER BY banned_at DESC")->fetchAll();
        
        require_once '../app/views/admin/layout.php';
    }

    public function banIp() {
        if (!$this->isAuthenticated()) exit("Unauthorized");
        $ip = $_POST['ip'] ?? '';
        $reason = $_POST['reason'] ?? 'Banned by Admin';
        if ($ip) {
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            $stmt = $pdo->prepare("INSERT IGNORE INTO ip_bans (ip_address, reason) VALUES (?, ?)");
            $stmt->execute([$ip, $reason]);
        }
        header('Location: /admin/users');
    }

    public function unbanIp() {
        if (!$this->isAuthenticated()) exit("Unauthorized");
        $ip = $_POST['ip'] ?? '';
        if ($ip) {
            $db = Database::getInstance();
            $db->query("DELETE FROM ip_bans WHERE ip_address = ?", [$ip]);
        }
        header('Location: /admin/users');
    }

    // --- Settings ---
    public function settings() {
        if (!$this->isAuthenticated()) exit(header('Location: /admin'));
        $pageTitle = "System Settings";
        $db = Database::getInstance();
        $settings = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        require_once '../app/views/admin/layout.php';
    }

    public function updateSettings() {
        if (!$this->isAuthenticated()) exit("Unauthorized");
        $db = Database::getInstance();
        
        foreach ($_POST as $key => $value) {
            if ($key !== 'action') { // Skip action param if exists
                $db->query("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?", [$key, $value, $value]);
            }
        }
        $_SESSION['success'] = "Settings saved.";
        header('Location: /admin/settings');
    }

    public function runUpdates() {
        if (!$this->isAuthenticated()) exit("Unauthorized");
        $db = Database::getInstance();
        $updateFiles = glob('../updates/*.sql');
        sort($updateFiles);
        $stmtVer = $db->query("SELECT version FROM app_version");
        $appliedVersions = $stmtVer->fetchAll(PDO::FETCH_COLUMN);
        
        $count = 0;
        foreach ($updateFiles as $file) {
            $filename = basename($file);
            if (!in_array($filename, $appliedVersions)) {
                // Use simple split for now, assuming updates are simple
                $sql = file_get_contents($file);
                try {
                    $db->getPdo()->exec($sql);
                    $db->query("INSERT INTO app_version (version) VALUES (?)", [$filename]);
                    $count++;
                } catch (Exception $e) { /* Log error */ }
            }
        }
        $_SESSION['success'] = "$count updates applied.";
        header('Location: /admin/dashboard');
    }
    
    // --- Tools ---
    public function testSmtp() {
        if (!$this->isAuthenticated()) exit("Unauthorized");
        $db = Database::getInstance();
        $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $smtp = new SMTP($s['smtp_host'], $s['smtp_port'], $s['smtp_user'], $s['smtp_pass']);
        $result = $smtp->send(
            $s['smtp_from_email'], // Send to self
            "SMTP Test", 
            "<h1>It Works!</h1><p>Your SMTP settings are correct.</p>", 
            $s['smtp_from_email'], 
            $s['smtp_from_name']
        );
        
        if ($result) $_SESSION['success'] = "Test email sent successfully!";
        else $_SESSION['error'] = "Failed to send email. Check logs.";
        
        header('Location: /admin/settings');
    }

    // --- CMS Pages ---
    public function pages() {
        if (!$this->isAuthenticated()) exit(header('Location: /admin'));
        $pageTitle = "Manage Pages";
        $db = Database::getInstance();
        $pages = $db->query("SELECT * FROM pages ORDER BY id DESC")->fetchAll();
        require_once '../app/views/admin/layout.php';
    }

    public function editPage($id = null) {
        if (!$this->isAuthenticated()) exit(header('Location: /admin'));
        $pageTitle = $id ? "Edit Page" : "New Page";
        $page = [];
        if ($id) {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT * FROM pages WHERE id = ?", [$id]);
            $page = $stmt->fetch();
        }
        require_once '../app/views/admin/layout.php';
    }

    public function savePage() {
        if (!$this->isAuthenticated()) exit("Unauthorized");
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'];
        $content = $_POST['content'];
        $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        $db = Database::getInstance();
        if ($id) {
            $db->query("UPDATE pages SET title=?, slug=?, content=? WHERE id=?", [$title, $slug, $content, $id]);
        } else {
            $db->query("INSERT INTO pages (title, slug, content) VALUES (?, ?, ?)", [$title, $slug, $content]);
        }
        
        $_SESSION['success'] = "Page saved successfully.";
        header('Location: /admin/pages');
    }

    public function deletePage($id) {
        if (!$this->isAuthenticated()) exit("Unauthorized");
        $db = Database::getInstance();
        $db->query("DELETE FROM pages WHERE id = ?", [$id]);
        $_SESSION['success'] = "Page deleted.";
        header('Location: /admin/pages');
    }
}
