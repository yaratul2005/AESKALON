<?php
// Great10 Installer & Auto-Fixer tools
// Access this via yourbrowser.com/install.php

// 1. Load Config
$configFile = '../config/config.php';
if (!file_exists($configFile)) {
    die("Error: config/config.php not found. Please create it first.");
}
require_once $configFile;

$messages = [];

function logMsg($msg, $type = 'info') {
    global $messages;
    $messages[] = ['type' => $type, 'text' => $msg];
}

// 2. Database Connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    logMsg("Database connection successful.", 'success');
} catch (PDOException $e) {
    // Attempt to create database if it doesn't exist
    try {
        $dsnNoDb = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
        $pdoRoot = new PDO($dsnNoDb, DB_USER, DB_PASS);
        $pdoRoot->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
        logMsg("Database '" . DB_NAME . "' did not exist, so I created it.", 'success');
        
        // Retry connection
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $ex) {
        die("<h1>Critical Error</h1><p>Could not connect to database. Check your config.php.</p><pre>" . $ex->getMessage() . "</pre>");
    }
}

// 3. Helper to parse and execute SQL
function splitSql($sql) {
    $queries = [];
    $buffer = '';
    $inString = false;
    $len = strlen($sql);

    for ($i = 0; $i < $len; $i++) {
        $char = $sql[$i];
        if ($inString && $char === '\\' && $i + 1 < $len) {
            $buffer .= $char . $sql[$i + 1];
            $i++; continue;
        }
        if ($char === "'") $inString = !$inString;
        if ($char === ';' && !$inString) {
            $q = trim($buffer);
            if (!empty($q)) $queries[] = $q;
            $buffer = '';
        } else {
            $buffer .= $char;
        }
    }
    $q = trim($buffer);
    if (!empty($q)) $queries[] = $q;
    return $queries;
}

function runSqlFile($pdo, $file) {
    if (!file_exists($file)) return false;
    $sql = file_get_contents($file);
    $lines = explode("\n", $sql);
    $cleanLines = [];
    foreach ($lines as $line) {
        $trim = trim($line);
        if (strpos($trim, '--') === 0 || strpos($trim, '#') === 0) continue;
        $cleanLines[] = $line;
    }
    $sql = implode("\n", $cleanLines);
    $queries = splitSql($sql);
    foreach ($queries as $query) {
        if (!empty($query)) {
            try { $pdo->exec($query); } catch (Exception $e) {
                logMsg("Query Warning in " . basename($file) . ": " . $e->getMessage(), 'warning');
            }
        }
    }
    return true;
}

// 4. Run Base Schema
logMsg("Checking database schema...");
$schemaFile = '../database/schema.sql';
runSqlFile($pdo, $schemaFile);
logMsg("Base schema check completed.", 'type-info');

// 5. Run Updates
logMsg("Checking for updates...");
$updateFiles = glob('../updates/*.sql');
sort($updateFiles);
try {
    $stmtVer = $pdo->query("SELECT version FROM app_version");
    $appliedVersions = $stmtVer->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $appliedVersions = [];
}
foreach ($updateFiles as $file) {
    $filename = basename($file);
    if (!in_array($filename, $appliedVersions)) {
        if (runSqlFile($pdo, $file)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO app_version (version) VALUES (?)");
                $stmt->execute([$filename]);
                logMsg("Applied update: $filename", 'success');
            } catch (Exception $e) {}
        }
    }
}

// 6. Handle Admin User Creation
$adminCreated = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Remove existing default admin if exists or any collision
        // We will INSERT or UPDATE
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = ?");
            $stmt->execute([$username, $hash, $hash]);
            logMsg("Admin account '$username' created/updated successfully!", 'success');
            $adminCreated = true;
        } catch (Exception $e) {
            logMsg("Error creating admin: " . $e->getMessage(), 'error');
        }
    } else {
        logMsg("Username and Password are required.", 'error');
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Great10 Installer</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; max-width: 800px; margin: 0 auto; line-height: 1.5; }
        .log-container { background: #1e293b; padding: 1.5rem; border-radius: 8px; border: 1px solid #334155; margin-bottom: 2rem; max-height: 200px; overflow-y: auto; }
        .item { margin-bottom: 0.5rem; padding: 0.5rem; border-radius: 4px; font-size: 0.9rem; }
        .type-info { color: #94a3b8; }
        .type-success { color: #4ade80; background: rgba(74, 222, 128, 0.1); }
        .type-warning { color: #facc15; background: rgba(250, 204, 21, 0.1); }
        .type-error { color: #f87171; background: rgba(248, 113, 113, 0.1); font-weight: bold; }
        h1 { color: #38bdf8; margin-bottom: 0.5rem; }
        h2 { color: #e2e8f0; margin-top: 0; border-bottom: 1px solid #334155; padding-bottom: 0.5rem; }
        
        .card { background: #1e293b; padding: 2rem; border-radius: 8px; border: 1px solid #334155; }
        input { width: 100%; padding: 0.75rem; margin-bottom: 1rem; background: #0f172a; border: 1px solid #334155; color: white; border-radius: 6px; box-sizing: border-box; }
        label { display: block; margin-bottom: 0.5rem; color: #94a3b8; font-weight: 500; }
        .btn { display: inline-block; background: #38bdf8; color: #0f172a; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; border: none; cursor: pointer; font-size: 1rem; }
        .btn:hover { opacity: 0.9; }
        .btn-outline { background: transparent; border: 1px solid #38bdf8; color: #38bdf8; }
        
        .success-box { background: rgba(74, 222, 128, 0.1); border: 1px solid #4ade80; color: #4ade80; padding: 1rem; border-radius: 6px; text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
    <h1>Great10 Installer</h1>
    <p style="color: #94a3b8; margin-bottom: 2rem;">System Status & Configuration</p>
    
    <div class="log-container">
        <?php foreach ($messages as $msg): ?>
            <div class="item type-<?= $msg['type'] ?>">
                <?php if($msg['type']=='success') echo '✔ '; ?>
                <?php if($msg['type']=='error') echo '✖ '; ?>
                <?= htmlspecialchars($msg['text']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <h2>Admin Account Setup</h2>
        <?php if ($adminCreated): ?>
            <div class="success-box">
                <h3>Account Created Successfully!</h3>
                <p>You can now log in to the admin panel.</p>
                <br>
                <a href="/admin" class="btn">Go to Admin Login</a>
            </div>
        <?php else: ?>
            <p>Create or update the administrator account for your CMS.</p>
            <form method="POST">
                <input type="hidden" name="create_admin" value="1">
                
                <label>Admin Username / Email</label>
                <input type="text" name="username" placeholder="e.g. admin@great10.xyz" required>
                
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter a strong password" required>
                
                <button type="submit" class="btn">Create Account</button>
            </form>
        <?php endif; ?>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
         <a href="/" style="color: #94a3b8; text-decoration: none;">Return to Homepage</a>
    </div>

</body>
</html>
