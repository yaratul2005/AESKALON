<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Start</h1>";

echo "<p>Loading SMTP...</p>";
try {
    require_once '../core/SMTP.php';
    echo "<p style='color:green'>SMTP Loaded</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>SMTP Error: " . $e->getMessage() . "</p>";
}

echo "<p>Loading GoogleAuth...</p>";
try {
    require_once '../core/GoogleAuth.php';
    echo "<p style='color:green'>GoogleAuth Loaded</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>GoogleAuth Error: " . $e->getMessage() . "</p>";
}

echo "<p>Loading AuthController...</p>";
try {
    require_once '../app/controllers/AuthController.php';
    echo "<p style='color:green'>AuthController Loaded</p>";
    
    $auth = new AuthController();
    echo "<p style='color:green'>AuthController Instantiated</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>AuthController Error: " . $e->getMessage() . "</p>";
}
