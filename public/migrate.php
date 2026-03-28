<?php
require_once '../config/config.php';
require_once '../core/Database.php';

try {
    $db = Database::getInstance();
    
    // Add facebook_id column if it doesn't exist
    $db->query("ALTER TABLE users ADD COLUMN facebook_id VARCHAR(255) NULL AFTER google_id");
    echo "<h3 style='color: green;'>Successfully added 'facebook_id' column to users table!</h3>";
    
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
         echo "<h3 style='color: green;'>The 'facebook_id' column already exists!</h3>";
    } else {
         echo "<h3 style='color: red;'>Error updating users: " . htmlspecialchars($e->getMessage()) . "</h3>";
    }
}

try {
    $db = Database::getInstance();
    $settings = [
        'facebook_app_id' => '',
        'facebook_app_secret' => '',
        'facebook_redirect_uri' => ''
    ];
    foreach ($settings as $key => $val) {
        $db->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)", [$key, $val]);
    }
    echo "<h3 style='color: green;'>Successfully populated default Facebook settings!</h3>";
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error updating settings: " . htmlspecialchars($e->getMessage()) . "</h3>";
}

echo "<hr>";
echo "<h2>Migration Complete!</h2>";
echo "<p>Please delete this file (`public/migrate.php`) for security, then test your Facebook Login again.</p>";
