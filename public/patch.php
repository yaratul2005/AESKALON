<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getInstance();

    // 1. User Ratings Table 
    $db->query("CREATE TABLE IF NOT EXISTS user_ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        tmdb_id INT NOT NULL,
        type VARCHAR(20) NOT NULL,
        rating TINYINT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_rating (user_id, tmdb_id, type)
    )");

    // 2. Stream Reports Table (Flag Dead Servers)
    $db->query("CREATE TABLE IF NOT EXISTS stream_reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        tmdb_id INT NOT NULL,
        type VARCHAR(20) NOT NULL,
        server_id INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "<div style='font-family:sans-serif; padding:40px; background:#111; color:#fff; text-align:center;'>";
    echo "<h1 style='color:#4ade80;'>✅ SUCCESS!</h1>";
    echo "<h2>Watch schemas (User Ratings & Stream Reports) successfully created in MySQL.</h2>";
    echo "<p>The 10-star rating features and stream reporting should now work perfectly.</p>";
    echo "<p style='color:#f43f5e; font-weight:bold;'>For security, please delete this 'patch.php' file from your server.</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='font-family:sans-serif; padding:40px; background:#111; color:#fff; text-align:center;'>";
    echo "<h1 style='color:#f43f5e;'>❌ SQL Migration Failed</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
