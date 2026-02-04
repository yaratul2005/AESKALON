<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';
require_once '../core/Database.php';
require_once '../core/SMTP.php';

echo "<h1>SMTP Debugger</h1>";

try {
    $db = Database::getInstance();
    $s = $db->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo "<p><strong>Host:</strong> " . htmlspecialchars($s['smtp_host']) . "</p>";
    echo "<p><strong>Port:</strong> " . htmlspecialchars($s['smtp_port']) . "</p>";
    echo "<p><strong>User:</strong> " . htmlspecialchars($s['smtp_user']) . "</p>";
    echo "<p><strong>From:</strong> " . htmlspecialchars($s['smtp_from_email']) . "</p>";
    
    $smtp = new SMTP($s['smtp_host'], (int)$s['smtp_port'], $s['smtp_user'], $s['smtp_pass']);
    
    echo "<h3>Attempting to send email...</h3>";
    echo "<pre style='background:#eee;padding:10px;'>";
    
    // We send to the 'From' email as a self-test
    $result = $smtp->send(
        $s['smtp_from_email'], 
        "SMTP Debug Test", 
        "If you see this, SMTP is working!", 
        $s['smtp_from_email'], 
        $s['site_name'] ?? 'Debug'
    );
    
    echo "</pre>";
    
    if ($result) {
        echo "<h2 style='color:green'>SUCCESS: Email accepted by server.</h2>";
    } else {
        echo "<h2 style='color:red'>FAILURE: Email rejected.</h2>";
    }
    
    echo "<h3>Debug Logs:</h3>";
    echo "<pre style='background:#ddd;padding:10px;'>";
    print_r($smtp->getLogs());
    echo "</pre>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>CRITICAL ERROR: " . $e->getMessage() . "</h2>";
}
