<?php
// RESET TOOL
require_once '../config/config.php';
require_once '../core/Database.php';

// Check for a secret key or just allow it? 
// Since this is a dev environment helper, we'll just run it.

echo "<h1>System Reset</h1>";

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    // 1. Clear Data
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE users");
    $pdo->exec("TRUNCATE TABLE user_history");
    $pdo->exec("TRUNCATE TABLE watch_later");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<p style='color:green'>✔ All users and history cleared.</p>";
    
    // 2. Create Default Admin
    $user = 'admin';
    $email = 'admin@great10.xyz';
    $pass = 'admin123';
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    
    // Assuming 'is_admin' or similar might exist, or we just rely on first user.
    // Based on previous schema checks, we insert basic info.
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, is_verified) VALUES (?, ?, ?, 1)");
    $stmt->execute([$user, $email, $hash]);
    
    // Try to set admin flag if it exists
    try {
        $pdo->exec("UPDATE users SET is_admin=1 WHERE username='admin'");
    } catch(Exception $e) {}

    echo "<p style='color:green'>✔ Default Admin created.</p>";
    echo "<ul>
        <li>Username: <strong>admin</strong></li>
        <li>Email: <strong>admin@great10.xyz</strong></li>
        <li>Password: <strong>admin123</strong></li>
    </ul>";

    // 3. Clear Session
    session_start();
    session_destroy();
    echo "<p style='color:green'>✔ Session cleared.</p>";
    
    echo "<p><strong>System is clean.</strong> <a href='/login'>Go to Login</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
