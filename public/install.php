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
function runSqlFile($pdo, $file) {
    if (!file_exists($file)) return false;
    
    $sql = file_get_contents($file);
    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/#.*$/m', '', $sql);
    
    // Split by semicolon
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    $errors = 0;
    foreach ($queries as $query) {
        if (!empty($query)) {
            try {
                $pdo->exec($query);
            } catch (Exception $e) {
                // Ignore specific errors like 'Duplicate column' if strictly necessary, 
                // but usually we want to know. For INSERT IGNORE / IF NOT EXISTS it should be fine.
                // We log but continue.
                logMsg("Query Warning in " . basename($file) . ": " . $e->getMessage(), 'warning');
            }
        }
    }
    return true;
}

// 4. Run Base Schema
logMsg("Checking database schema...");
$schemaFile = '../database/schema.sql';
if (runSqlFile($pdo, $schemaFile)) {
    logMsg("Base schema check/installation completed.", 'success');
} else {
    logMsg("Error: Schema file not found at $schemaFile", 'error');
}

// 5. Run Updates
logMsg("Checking for updates...");
$updateFiles = glob('../updates/*.sql');
sort($updateFiles);

// Check current version
// We might have just created the table, so we check if version table exists first (it should from schema)
try {
    $stmtVer = $pdo->query("SELECT version FROM app_version");
    $appliedVersions = $stmtVer->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $appliedVersions = [];
    logMsg("Version table missing or empty, assuming fresh start or manual fix needed.", 'warning');
}

$updatesRun = 0;
foreach ($updateFiles as $file) {
    $filename = basename($file);
    if (!in_array($filename, $appliedVersions)) {
        if (runSqlFile($pdo, $file)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO app_version (version) VALUES (?)");
                $stmt->execute([$filename]);
                logMsg("Applied update: $filename", 'success');
                $updatesRun++;
            } catch (Exception $e) {
                logMsg("Failed to record version for $filename: " . $e->getMessage(), 'error');
            }
        }
    }
}

if ($updatesRun === 0) {
    logMsg("No new updates found.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation & Repair - Great10</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; max-width: 800px; margin: 0 auto; }
        .log { background: #1e293b; padding: 1.5rem; border-radius: 8px; border: 1px solid #334155; }
        .item { margin-bottom: 0.5rem; padding: 0.5rem; border-radius: 4px; }
        .type-info { color: #94a3b8; }
        .type-success { color: #4ade80; background: rgba(74, 222, 128, 0.1); }
        .type-warning { color: #facc15; background: rgba(250, 204, 21, 0.1); }
        .type-error { color: #f87171; background: rgba(248, 113, 113, 0.1); font-weight: bold; }
        h1 { color: #38bdf8; }
        .actions { margin-top: 2rem; }
        .btn { display: inline-block; background: #38bdf8; color: #0f172a; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .warning-box { margin-top: 2rem; padding: 1rem; border-left: 4px solid #facc15; background: rgba(250, 204, 21, 0.05); }
    </style>
</head>
<body>
    <h1>Installation & System Check</h1>
    
    <div class="log">
        <?php foreach ($messages as $msg): ?>
            <div class="item type-<?= $msg['type'] ?>">
                <?php if($msg['type']=='success') echo '✔ '; ?>
                <?php if($msg['type']=='error') echo '✖ '; ?>
                <?= htmlspecialchars($msg['text']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="actions">
        <a href="/" class="btn">Go to Homepage</a>
        <a href="/admin" class="btn" style="background: #1e293b; color: #38bdf8; border: 1px solid #38bdf8;">Go to Admin</a>
    </div>

    <div class="warning-box">
        <strong>Security Notice:</strong> For better security, please rename or delete this <code>install.php</code> file after you have successfully set up your site.
    </div>
</body>
</html>
