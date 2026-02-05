<?php
require_once '../config/config.php';
require_once '../core/Database.php';

$db = Database::getInstance();

$sql = "CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";

try {
    $db->query($sql);
    echo "Pages table created.\n";
    
    // Now seed
    $seedSql = file_get_contents('../updates/v3.0.1_legal_pages.sql');
    $db->query($seedSql);
    echo "Legal pages seeded.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "Done.\n";
