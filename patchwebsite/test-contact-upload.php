<?php
// /patchwebsite/test-contact-upload.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $targetDir = __DIR__ . '/uploads/temp/';
    $targetFile = $targetDir . basename($_FILES['file']['name']);
    
    echo "<h2>Upload Test</h2>";
    echo "File: " . $_FILES['file']['name'] . "<br>";
    echo "Size: " . $_FILES['file']['size'] . "<br>";
    echo "Temp: " . $_FILES['file']['tmp_name'] . "<br>";
    echo "Error: " . $_FILES['file']['error'] . "<br>";
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        echo "<p style='color:green;'>✅ Uploaded: <a href='uploads/temp/" . basename($_FILES['file']['name']) . "'>" . basename($_FILES['file']['name']) . "</a></p>";
    } else {
        echo "<p style='color:red;'>❌ move_uploaded_file failed!</p>";
        echo "Target: " . $targetFile . "<br>";
        echo "Permissions: " . substr(sprintf('%o', fileperms($targetDir)), -4) . "<br>";
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>