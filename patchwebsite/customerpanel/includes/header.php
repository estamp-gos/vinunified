<?php
// /patchwebsite/customerpanel/includes/header.php

require_once __DIR__ . '/config.php';
requireCustomerLogin();

$pageTitle = $pageTitle ?? 'Customer Dashboard';
$customerUser = getCustomerUser();
$pendingOrders = getCustomerPendingOrders();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - PunchyArts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/customer.css">
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/icon.png">
</head>
<body>

<div class="customer-wrapper">