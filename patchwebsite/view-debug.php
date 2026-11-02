<?php
// /patchwebsite/view-debug.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 Debug Logs</h1>";

$logFile = __DIR__ . '/logs/error.log';

if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $lines = explode("\n", $content);
    $lastLines = array_slice($lines, -200);
    
    echo "<div style='background: #0D0F12; color: #A0A5B5; padding: 20px; border-radius: 8px; font-family: monospace; font-size: 13px; overflow: auto; max-height: 600px;'>";
    echo "<h3 style='color: #00F0FF;'>Last 200 lines:</h3>";
    echo "<hr style='border-color: #2A2F38;'>";
    
    foreach ($lastLines as $line) {
        $line = htmlspecialchars($line);
        if (strpos($line, 'ORDER-DIGITIZING') !== false || 
            strpos($line, 'saveOrderArtwork') !== false ||
            strpos($line, 'FILES') !== false ||
            strpos($line, 'UPLOAD') !== false) {
            echo "<span style='color: #00F0FF;'>" . $line . "</span><br>";
        } elseif (strpos($line, 'ERROR') !== false || strpos($line, 'FAILED') !== false) {
            echo "<span style='color: #FF0055;'>" . $line . "</span><br>";
        } elseif (strpos($line, 'SUCCESS') !== false) {
            echo "<span style='color: #00FF88;'>" . $line . "</span><br>";
        } else {
            echo $line . "<br>";
        }
    }
    echo "</div>";
} else {
    echo "<p style='color: red;'>No log file found at: " . $logFile . "</p>";
}

echo "<hr>";
echo "<p><a href='" . SITE_URL . "customerpanel/order-digitizing.php' class='btn btn-primary-cyan'>Go to Digitizing Order</a></p>";
echo "<p><a href='" . SITE_URL . "' class='btn btn-outline-cyan'>Back to Website</a></p>";
?>