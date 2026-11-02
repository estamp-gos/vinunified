<?php
// /patchwebsite/adminpanel/download-all.php
require_once 'includes/config.php';
require_once 'includes/header.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$type = isset($_GET['type']) ? sanitize($_GET['type']) : 'artwork';

if ($orderId <= 0) {
    die('Invalid order ID.');
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM order_files WHERE order_id = ? AND file_type = ?");
$stmt->execute([$orderId, $type]);
$files = $stmt->fetchAll();

if (count($files) === 0) {
    die('No files found.');
}

// Create ZIP
$zip = new ZipArchive();
$zipName = 'order_' . $orderId . '_' . $type . '_' . date('Ymd') . '.zip';
$zipPath = sys_get_temp_dir() . '/' . $zipName;

if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die('Failed to create ZIP file.');
}

foreach ($files as $file) {
    $fullPath = __DIR__ . '/../' . $file['file_path'];
    if (file_exists($fullPath)) {
        $zip->addFile($fullPath, $file['file_name']);
    }
}

$zip->close();

// Send ZIP to browser
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($zipPath));
readfile($zipPath);

// Cleanup
unlink($zipPath);
exit;
?>