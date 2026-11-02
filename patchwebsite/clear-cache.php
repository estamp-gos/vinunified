<?php
// /patchwebsite/clear-cache.php

// ============================================
// CACHE CLEANER - PunchyArts
// ============================================

echo "<h1>🧹 Cache Cleaner</h1>";

// 1. Clear Session
session_start();
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
echo "<p>✅ Session cleared</p>";

// 2. Clear Browser Cache Headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
echo "<p>✅ Cache headers sent</p>";

// 3. Delete Temp Files (uploads/temp/)
$tempDir = __DIR__ . '/uploads/temp/';
if (is_dir($tempDir)) {
    $files = glob($tempDir . '*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    echo "<p>✅ $count temp files deleted</p>";
}

// 4. Delete Logs (optional)
$logFile = __DIR__ . '/logs/error.log';
if (file_exists($logFile)) {
    // Don't delete, just truncate
    file_put_contents($logFile, "[Cache cleared at " . date('Y-m-d H:i:s') . "]\n");
    echo "<p>✅ Log file truncated</p>";
}

echo "<hr>";
echo "<h3>✅ Cache Cleared Successfully!</h3>";
echo "<p>Redirecting to homepage in 3 seconds...</p>";
echo '<meta http-equiv="refresh" content="3;url=' . SITE_URL . '">';
echo '<p><a href="' . SITE_URL . '" class="btn btn-primary">Go to Homepage</a></p>';
?>