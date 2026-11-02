<?php
// /patchwebsite/customerpanel/download-all.php

require_once 'includes/config.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'artwork';
$userId = $_SESSION['user_id'];

if ($orderId <= 0) {
    header('Location: dashboard.php');
    exit;
}

$db = getDB();

// Verify order belongs to this customer
$stmt = $db->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: dashboard.php');
    exit;
}

// Get files
$stmt = $db->prepare("SELECT * FROM order_files WHERE order_id = ? AND file_type = ?");
$stmt->execute([$orderId, $type]);
$files = $stmt->fetchAll();

if (count($files) == 0) {
    setFlash('error', 'No files to download.');
    header('Location: order-details.php?id=' . $orderId);
    exit;
}

// Create zip file
$zip = new ZipArchive();
$zipName = 'order_' . $orderId . '_' . $type . '_' . date('Y-m-d') . '.zip';
$zipPath = sys_get_temp_dir() . '/' . $zipName;

if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    setFlash('error', 'Could not create zip file.');
    header('Location: order-details.php?id=' . $orderId);
    exit;
}

foreach ($files as $file) {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $file['file_path'];
    if (file_exists($filePath)) {
        $zip->addFile($filePath, $file['file_name']);
    }
}

$zip->close();

// Download the zip file
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($zipPath));
readfile($zipPath);

// Clean up
unlink($zipPath);
exit;
?>