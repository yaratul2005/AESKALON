<?php
// Whitespace Detector
$files = [
    '../config/config.php',
    '../core/Database.php',
    '../core/Router.php',
    '../core/SMTP.php',
    '../core/GoogleAuth.php',
    '../app/controllers/AuthController.php'
];

echo "<h1>Whitespace Check</h1>";

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "<p>File not found: $file</p>";
        continue;
    }
    
    $content = file_get_contents($file);
    $len = strlen($content);
    echo "<p><strong>$file</strong> ($len bytes): ";
    
    if (strpos($content, '<?php') !== 0) {
        echo "<span style='color:red'>Has leading content!</span>";
        echo " (First char code: " . ord($content[0]) . ")";
    } elseif (preg_match('/\?>\s+$/', $content)) {
        echo "<span style='color:red'>Has trailing whitespace after closing tag!</span>";
    } else {
        echo "<span style='color:green'>OK</span>";
    }
    echo "</p>";
}
