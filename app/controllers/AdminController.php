<?php

class AdminController {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function isAuthenticated() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

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

    public function dashboard() {
        if (!$this->isAuthenticated()) {
            header('Location: /admin');
            exit;
        }

        $db = Database::getInstance();
        
        // Fetch Settings
        $stmt = $db->query("SELECT * FROM settings");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [key => value]

        // Check for Updates
        $updateFiles = glob('../updates/*.sql');
        sort($updateFiles); // Apply in order
        
        // Find applied updates
        $stmtVer = $db->query("SELECT version FROM app_version");
        $appliedVersions = $stmtVer->fetchAll(PDO::FETCH_COLUMN);
        
        $pendingUpdates = [];
        foreach ($updateFiles as $file) {
            $filename = basename($file);
            if (!in_array($filename, $appliedVersions)) {
                $pendingUpdates[] = $filename;
            }
        }

        require_once '../app/views/admin/dashboard.php';
    }

    public function updateSettings() {
        if (!$this->isAuthenticated()) {
            die("Unauthorized");
        }

        $db = Database::getInstance();
        $settings = [
            'site_name' => $_POST['site_name'],
            'seo_description' => $_POST['seo_description'],
            'site_header_code' => $_POST['site_header_code'],
            'site_footer_code' => $_POST['site_footer_code']
        ];

        foreach ($settings as $key => $value) {
            $db->query("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?", [$key, $value, $value]);
        }

        $_SESSION['success'] = "Settings updated successfully!";
        header('Location: /admin/dashboard');
    }

    public function runUpdates() {
        if (!$this->isAuthenticated()) {
            die("Unauthorized");
        }

        $db = Database::getInstance();
        $updateFiles = glob('../updates/*.sql');
        sort($updateFiles);

        $stmtVer = $db->query("SELECT version FROM app_version");
        $appliedVersions = $stmtVer->fetchAll(PDO::FETCH_COLUMN);

        $count = 0;
        foreach ($updateFiles as $file) {
            $filename = basename($file);
            if (!in_array($filename, $appliedVersions)) {
                $sql = file_get_contents($file);
                try {
                    $db->getPdo()->exec($sql);
                    $db->query("INSERT INTO app_version (version) VALUES (?)", [$filename]);
                    $count++;
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error applying $filename: " . $e->getMessage();
                    header('Location: /admin/dashboard');
                    exit;
                }
            }
        }

        $_SESSION['success'] = "$count updates applied successfully!";
        header('Location: /admin/dashboard');
    }
}
