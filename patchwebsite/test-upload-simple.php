<?php
// /patchwebsite/test-upload-simple.php

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/security.php';
require_once 'includes/upload.php';

echo "<h1>📁 Simple Upload Test</h1>";

$targetDir = UPLOAD_PATH_TEMP;
echo "Target: " . $targetDir . "<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $result = uploadFile($_FILES['file'], UPLOAD_PATH_TEMP);
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    if ($result['success']) {
        echo "<p style='color:green;'>✅ File uploaded: <a href='" . SITE_URL . "uploads/temp/" . $result['filename'] . "' target='_blank'>" . SITE_URL . "uploads/temp/" . $result['filename'] . "</a></p>";
    } else {
        echo "<p style='color:red;'>❌ Upload failed: " . $result['error'] . "</p>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>