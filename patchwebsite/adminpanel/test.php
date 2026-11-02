<?php
// /patchwebsite/adminpanel/test.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔧 Admin Panel Test</h1>";

try {
    require_once __DIR__ . '/../../includes/config.php';
    echo "✅ Config loaded<br>";
} catch (Exception $e) {
    echo "❌ Config error: " . $e->getMessage() . "<br>";
}

try {
    require_once __DIR__ . '/../../includes/functions.php';
    echo "✅ Functions loaded<br>";
} catch (Exception $e) {
    echo "❌ Functions error: " . $e->getMessage() . "<br>";
}

try {
    require_once __DIR__ . '/../../includes/auth.php';
    echo "✅ Auth loaded<br>";
} catch (Exception $e) {
    echo "❌ Auth error: " . $e->getMessage() . "<br>";
}

try {
    $db = getDB();
    echo "✅ Database connected<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "SITE_URL: " . SITE_URL . "<br>";
echo "ADMIN_URL: " . ADMIN_URL . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "<br>";