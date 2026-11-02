<?php
// /patchwebsite/test-upload.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/security.php';
require_once 'includes/upload.php';

echo "<h1>📁 Test File Upload</h1>";

$targetDir = UPLOAD_PATH_TEMP;
echo "Target Dir: " . $targetDir . "<br>";

// Check directory
if (is_dir($targetDir)) {
    echo "✅ Directory exists<br>";
} else {
    echo "❌ Directory does NOT exist<br>";
    mkdir($targetDir, 0755, true);
    echo "Created directory<br>";
}

// Check writable
if (is_writable($targetDir)) {
    echo "✅ Directory is writable<br>";
} else {
    echo "❌ Directory is NOT writable<br>";
    chmod($targetDir, 0755);
    echo "Tried to fix permissions<br>";
}

// Check PHP upload limits
echo "<hr>";
echo "<h3>PHP Upload Limits:</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";

// Test actual upload
echo "<hr>";
echo "<h3>Test Upload Form:</h3>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "File received: " . $_FILES['test_file']['name'] . "<br>";
    echo "File size: " . $_FILES['test_file']['size'] . "<br>";
    echo "File error: " . $_FILES['test_file']['error'] . "<br>";
    echo "File tmp_name: " . $_FILES['test_file']['tmp_name'] . "<br>";
    
    $result = uploadFile($_FILES['test_file'], UPLOAD_PATH_TEMP);
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    if ($result['success']) {
        echo "<p style='color: green;'>✅ File uploaded: " . $result['filename'] . "</p>";
        echo "<p>URL: <a href='" . SITE_URL . "uploads/temp/" . $result['filename'] . "' target='_blank'>" . SITE_URL . "uploads/temp/" . $result['filename'] . "</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Upload failed: " . $result['error'] . "</p>";
    }
}

echo "<hr>";
echo '
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file" required>
    <button type="submit">Upload</button>
</form>
';
?>