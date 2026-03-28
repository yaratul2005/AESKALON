<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
$db = Database::getInstance();
try {
    $db->query("ALTER TABLE users ADD COLUMN facebook_id VARCHAR(255) NULL AFTER google_id");
    echo "Column facebook_id added.\n";
} catch (Exception $e) { 
    echo "Info: " . $e->getMessage() . "\n"; 
}
