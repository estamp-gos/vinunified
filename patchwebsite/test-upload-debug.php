<?php
// /patchwebsite/test-upload-debug.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/security.php';
require_once 'includes/upload.php';

echo "<h1>🔍 File Upload Debug</h1>";

// Check if upload function exists
if (function_exists('uploadFile')) {
    echo "✅ uploadFile() function exists<br>";
} else {
    echo "❌ uploadFile() function MISSING!<br>";
}

if (function_exists('validateFileUpload')) {
    echo "✅ validateFileUpload() function exists<br>";
} else {
    echo "❌ validateFileUpload() function MISSING!<br>";
}

// Check directory permissions
$tempDir = UPLOAD_PATH_TEMP;
echo "<br><strong>Temp Directory:</strong> " . $tempDir . "<br>";

if (is_dir($tempDir)) {
    echo "✅ Directory exists<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($tempDir)), -4) . "<br>";
    echo "Writable: " . (is_writable($tempDir) ? '✅ Yes' : '❌ No') . "<br>";
} else {
    echo "❌ Directory does NOT exist<br>";
    mkdir($tempDir, 0777, true);
    echo "Created directory<br>";
}

// Test file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<hr>";
    echo "<h3>Upload Result:</h3>";
    
    $file = $_FILES['test_file'];
    echo "File Name: " . $file['name'] . "<br>";
    echo "File Size: " . $file['size'] . " bytes<br>";
    echo "Temp Path: " . $file['tmp_name'] . "<br>";
    echo "Error Code: " . $file['error'] . "<br>";
    
    // Check if file exists in temp
    if (file_exists($file['tmp_name'])) {
        echo "✅ Temp file exists<br>";
    } else {
        echo "❌ Temp file does NOT exist!<br>";
    }
    
    // Try to upload
    $result = uploadFile($file, UPLOAD_PATH_TEMP);
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    if ($result['success']) {
        echo "<p style='color:green;'>✅ Upload successful! File: <a href='" . SITE_URL . "uploads/temp/" . $result['filename'] . "' target='_blank'>" . SITE_URL . "uploads/temp/" . $result['filename'] . "</a></p>";
    } else {
        echo "<p style='color:red;'>❌ Upload failed: " . $result['error'] . "</p>";
    }
}

echo "<hr>";
echo "<h3>Test Upload Form:</h3>";
echo '<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file" required>
    <button type="submit">Upload</button>
</form>';
?>