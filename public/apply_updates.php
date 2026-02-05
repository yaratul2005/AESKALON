<?php
require_once '../config/config.php';
require_once '../core/Database.php';

$db = Database::getInstance();
$sql = file_get_contents('../updates/v3.0.0_user_features.sql');

// Split queries robustly
$queries = preg_split('/;\s*[\r\n]+/', $sql);

foreach ($queries as $q) {
    if (trim($q)) {
        try {
            $db->query($q);
            echo "Executed: " . substr($q, 0, 50) . "...\n";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
echo "Done.\n";
