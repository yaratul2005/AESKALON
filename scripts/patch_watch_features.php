<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getInstance();

    // 1. User Ratings Table (10-Star Hover)
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

    echo "Watch features schemas successfully initialized.\n";
} catch (Exception $e) {
    echo "SQL Migration Failed: " . $e->getMessage() . "\n";
}
