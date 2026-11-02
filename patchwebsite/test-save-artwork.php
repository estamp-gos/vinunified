<?php
// /patchwebsite/test-save-artwork.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/security.php';
require_once 'includes/upload.php';

echo "<h1>🔍 Test saveOrderArtwork()</h1>";

// First create a real order
$db = getDB();
$userId = 2; // Use an existing user ID from your database

$stmt = $db->prepare("INSERT INTO orders (order_number, user_id, service_type, status) VALUES (?, ?, 'digitizing', 'submitted')");
$orderNumber = 'TEST-' . time();
$stmt->execute([$orderNumber, $userId]);
$realOrderId = $db->lastInsertId();

echo "Created test order with ID: " . $realOrderId . "<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<hr>";
    echo "<h3>Testing saveOrderArtwork() with Real Order ID: $realOrderId</h3>";
    
    $result = saveOrderArtwork($_FILES['test_file'], $realOrderId);
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    if ($result['success']) {
        echo "<p style='color:green;'>✅ " . $result['count'] . " files saved!</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed: " . $result['error'] . "</p>";
    }
}

echo "<hr>";
echo "<h3>Test Form:</h3>";
echo '<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file[]" multiple required>
    <button type="submit">Test saveOrderArtwork()</button>
</form>';
?>