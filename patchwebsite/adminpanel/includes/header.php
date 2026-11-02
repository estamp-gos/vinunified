<?php
// /patchwebsite/adminpanel/includes/header.php

// ============================================
// ADMIN HEADER - Include this in all admin pages
// ============================================

// Load config and require login
require_once __DIR__ . '/config.php';
requireAdminLogin();

// Set default page title if not set
$pageTitle = $pageTitle ?? 'Admin Panel';

// Get admin user info
$adminUser = getAdminUser();
$pendingOrders = getPendingOrderCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - PunchyArts Admin</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    
    <?php if (file_exists(__DIR__ . '/../../assets/css/admin.css')): ?>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/admin.css">
    <?php else: ?>
    <style>
        /* Fallback admin styles if admin.css is missing */
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #181B20; border-right: 1px solid #2A2F38; min-height: 100vh; padding: 20px 0; flex-shrink: 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .admin-sidebar .sidebar-brand { padding: 0 20px 20px; border-bottom: 1px solid #2A2F38; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .admin-sidebar .sidebar-brand span { font-weight: 700; color: #FFFFFF; font-size: 18px; }
        .admin-sidebar .sidebar-brand span::after { content: ' Admin'; color: #00F0FF; font-weight: 300; }
        .admin-sidebar .sidebar-nav { list-style: none; padding: 0; margin: 0; }
        .admin-sidebar .sidebar-nav li { margin: 2px 10px; border-radius: 8px; }
        .admin-sidebar .sidebar-nav li.active { background: rgba(0, 240, 255, 0.08); }
        .admin-sidebar .sidebar-nav li.active a { color: #00F0FF; }
        .admin-sidebar .sidebar-nav li a { display: flex; align-items: center; gap: 12px; padding: 10px 16px; color: #A0A5B5; text-decoration: none; border-radius: 6px; transition: all 0.3s ease; font-size: 14px; }
        .admin-sidebar .sidebar-nav li a:hover { background: rgba(0, 240, 255, 0.05); color: #FFFFFF; }
        .admin-sidebar .sidebar-nav li a i { width: 20px; font-size: 16px; }
        .admin-sidebar .sidebar-nav li a .badge { margin-left: auto; background: #FF0055; color: #FFFFFF; font-size: 11px; padding: 2px 10px; border-radius: 20px; }
        .admin-content { flex: 1; padding: 30px 35px; background: #0D0F12; min-height: 100vh; }
        .admin-header { margin-bottom: 30px; }
        .admin-header h1 { font-size: 28px; font-weight: 800; margin-bottom: 4px; }
        .admin-header p { color: #A0A5B5; margin-bottom: 0; }
        @media (max-width: 768px) { .admin-wrapper { flex-direction: column; } .admin-sidebar { width: 100%; min-height: auto; height: auto; position: static; padding: 10px 0; } .admin-sidebar .sidebar-nav { display: flex; flex-wrap: wrap; padding: 0 10px; gap: 4px; } .admin-sidebar .sidebar-nav li { margin: 0; } .admin-sidebar .sidebar-nav li a { padding: 6px 12px; font-size: 13px; } .admin-content { padding: 16px; } }
    </style>
    <?php endif; ?>
    
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/icon.png">
</head>
<body>

<!-- Admin Wrapper -->
<div class="admin-wrapper">