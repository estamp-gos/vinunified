<?php
// /patchwebsite/includes/upload.php

// ============================================
// FILE UPLOAD FUNCTIONS - COMPLETE FIXED
// ============================================

// ✅ IMPORTANT: Include security.php for validateFileUpload()
require_once __DIR__ . '/security.php';

// NOTE: getFileExtension(), getFileSize(), isAllowedFile(), generateUniqueFileName() are in functions.php

function uploadFile($file, $targetDir, $allowedTypes = null, $maxSize = null) {
    $allowedTypes = $allowedTypes ?? ALLOWED_EXTENSIONS;
    $maxSize = $maxSize ?? MAX_FILE_SIZE;
    
    error_log("UPLOAD: File: " . $file['name']);
    error_log("UPLOAD: Target Dir: " . $targetDir);
    error_log("UPLOAD: Temp: " . $file['tmp_name']);
    error_log("UPLOAD: Size: " . $file['size']);
    error_log("UPLOAD: Error: " . $file['error']);
    
    // ✅ Now validateFileUpload() is available from security.php
    if (!function_exists('validateFileUpload')) {
        error_log("UPLOAD: validateFileUpload() not found! Loading security.php...");
        require_once __DIR__ . '/security.php';
    }
    
    $validation = validateFileUpload($file);
    if (!$validation['valid']) {
        error_log("UPLOAD: Validation failed: " . $validation['error']);
        return ['success' => false, 'error' => $validation['error']];
    }
    
    if (!is_dir($targetDir)) {
        error_log("UPLOAD: Creating directory: " . $targetDir);
        if (!mkdir($targetDir, 0777, true)) {
            error_log("UPLOAD: Failed to create directory!");
            return ['success' => false, 'error' => 'Failed to create upload directory.'];
        }
    }
    
    chmod($targetDir, 0777);
    
    if (!is_writable($targetDir)) {
        error_log("UPLOAD: Directory not writable: " . $targetDir);
        chmod($targetDir, 0777);
        if (!is_writable($targetDir)) {
            return ['success' => false, 'error' => 'Upload directory is not writable.'];
        }
    }
    
    $ext = getFileExtension($file['name']);
    $newName = uniqid() . '_' . time() . '.' . $ext;
    $targetPath = rtrim($targetDir, '/') . '/' . $newName;
    
    error_log("UPLOAD: Target path: " . $targetPath);
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        chmod($targetPath, 0644);
        error_log("UPLOAD: SUCCESS! File saved: " . $newName);
        return [
            'success' => true,
            'filename' => $newName,
            'original_name' => $file['name'],
            'path' => $targetPath,
            'size' => $file['size'],
            'mime' => $file['type'] ?? ''
        ];
    }
    
    $error = error_get_last();
    error_log("UPLOAD: move_uploaded_file FAILED! Error: " . ($error['message'] ?? 'Unknown'));
    error_log("UPLOAD: Source: " . $file['tmp_name'] . " -> Target: " . $targetPath);
    
    return ['success' => false, 'error' => 'Failed to upload file. Check error logs.'];
}

function uploadMultipleFiles($files, $targetDir, $allowedTypes = null, $maxSize = null) {
    $results = [];
    $errors = [];
    
    error_log("=== uploadMultipleFiles START ===");
    error_log("Files: " . print_r($files, true));
    
    if (!isset($files['name']) || empty($files['name'][0])) {
        error_log("uploadMultipleFiles: No files selected!");
        return ['success' => false, 'error' => 'No files selected.'];
    }
    
    $fileCount = count($files['name']);
    error_log("uploadMultipleFiles: File count: " . $fileCount);
    
    if ($fileCount > MAX_FILES_PER_ORDER) {
        return ['success' => false, 'error' => 'Maximum ' . MAX_FILES_PER_ORDER . ' files allowed.'];
    }
    
    for ($i = 0; $i < $fileCount; $i++) {
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];
        
        error_log("uploadMultipleFiles: Processing file " . $i . ": " . $file['name']);
        
        $result = uploadFile($file, $targetDir, $allowedTypes, $maxSize);
        
        if ($result['success']) {
            $results[] = $result;
            error_log("uploadMultipleFiles: File " . $i . " uploaded successfully");
        } else {
            $errors[] = $result['error'];
            error_log("uploadMultipleFiles: File " . $i . " failed: " . $result['error']);
        }
    }
    
    if (empty($results)) {
        error_log("uploadMultipleFiles: No files uploaded successfully!");
        return ['success' => false, 'error' => implode(', ', $errors)];
    }
    
    error_log("uploadMultipleFiles: " . count($results) . " files uploaded successfully");
    
    return [
        'success' => true,
        'files' => $results,
        'errors' => $errors
    ];
}

function saveOrderArtwork($files, $orderId) {
    $targetDir = UPLOAD_PATH_ARTWORK . $orderId . '/';
    
    error_log("=== saveOrderArtwork START ===");
    error_log("Order ID: " . $orderId);
    error_log("Target Dir: " . $targetDir);
    
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            error_log("saveOrderArtwork: Failed to create directory!");
            return ['success' => false, 'error' => 'Failed to create directory.'];
        }
        error_log("saveOrderArtwork: Directory created: " . $targetDir);
    }
    
    $result = uploadMultipleFiles($files, $targetDir);
    error_log("saveOrderArtwork: uploadMultipleFiles result: " . print_r($result, true));
    
    if ($result['success'] && !empty($result['files'])) {
        $db = getDB();
        $count = 0;
        foreach ($result['files'] as $file) {
            $filePath = 'uploads/artwork/' . $orderId . '/' . $file['filename'];
            $stmt = $db->prepare("INSERT INTO order_files (order_id, file_name, file_path, file_type) VALUES (?, ?, ?, 'artwork')");
            $stmt->execute([
                $orderId,
                $file['original_name'],
                $filePath
            ]);
            $count++;
            error_log("saveOrderArtwork: DB insert successful for: " . $file['original_name'] . " -> " . $filePath);
        }
        error_log("saveOrderArtwork: " . $count . " files saved to database");
        return ['success' => true, 'files' => $result['files'], 'count' => $count];
    }
    
    error_log("saveOrderArtwork: No files to save!");
    return ['success' => false, 'error' => 'No files uploaded.'];
}

function saveProductionFile($file, $orderId) {
    $targetDir = UPLOAD_PATH_PRODUCTION . $orderId . '/';
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $result = uploadFile($file, $targetDir);
    
    if ($result['success']) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO order_files (order_id, file_name, file_path, file_type) VALUES (?, ?, ?, 'production')");
        $stmt->execute([
            $orderId,
            $result['original_name'],
            'uploads/production/' . $orderId . '/' . $result['filename']
        ]);
    }
    
    return $result;
}

function uploadPortfolioImage($file) {
    $targetDir = UPLOAD_PATH_PORTFOLIO;
    return uploadFile($file, $targetDir);
}

function uploadBlogImage($file) {
    $targetDir = UPLOAD_PATH_BLOG;
    return uploadFile($file, $targetDir);
}

function deleteUploadedFile($path) {
    $fullPath = __DIR__ . '/../' . $path;
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

function getFileUrl($path) {
    return SITE_URL . ltrim($path, '/');
}

function fileExists($path) {
    $fullPath = __DIR__ . '/../' . $path;
    return file_exists($fullPath);
}

function getFileInfo($path) {
    $fullPath = __DIR__ . '/../' . $path;
    if (!file_exists($fullPath)) {
        return null;
    }
    
    return [
        'name' => basename($path),
        'path' => $path,
        'url' => getFileUrl($path),
        'size' => filesize($fullPath),
        'size_formatted' => getFileSize(filesize($fullPath)),
        'mime' => mime_content_type($fullPath),
        'ext' => getFileExtension($path),
        'modified' => filemtime($fullPath)
    ];
}

function getFilesInDirectory($dir, $recursive = false) {
    $files = [];
    $fullPath = __DIR__ . '/../' . ltrim($dir, '/');
    
    if (!is_dir($fullPath)) {
        return $files;
    }
    
    $items = scandir($fullPath);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $itemPath = $fullPath . '/' . $item;
        if (is_file($itemPath)) {
            $files[] = $dir . '/' . $item;
        } elseif ($recursive && is_dir($itemPath)) {
            $subFiles = getFilesInDirectory($dir . '/' . $item, true);
            $files = array_merge($files, $subFiles);
        }
    }
    
    return $files;
}
?>