<?php
require_once '../config/config.php';
require_once '../core/Database.php';

$db = Database::getInstance();
$sql = file_get_contents('../updates/v3.0.1_legal_pages.sql');
try {
    $db->query($sql);
    echo "Legal pages seeded successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "Done.\n";
