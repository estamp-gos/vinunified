<?php
// /patchwebsite/includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? SITE_NAME; ?></title>
    <meta name="description" content="<?php echo META_DESCRIPTION; ?>">
    <meta name="keywords" content="<?php echo META_KEYWORDS; ?>">
    <!-- USA Based Brand -->
    <link rel="alternate" hreflang="en-us" href="<?php echo SITE_URL; ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/responsive.css">
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/icon.png">
</head>
<body>

<!-- Top Bar - USA Contact Info -->
<div class="top-bar">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small><i class="fas fa-phone-alt text-cyan"></i> +1 (800) 123-4567</small>
                <small class="ms-3"><i class="fas fa-envelope text-cyan"></i> info@punchyarts.com</small>
            </div>
            <div class="col-md-6 text-end">
                <small><i class="fas fa-clock text-cyan"></i> Mon - Fri: 9:00 AM - 6:00 PM EST</small>
            </div>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
            <img src="<?php echo SITE_URL; ?>assets/images/logo-horizontal.png" alt="<?php echo SITE_NAME; ?>" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link <?php echo ($activePage ?? '') === 'home' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($activePage ?? '') === 'services' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>services">Services</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($activePage ?? '') === 'portfolio' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>portfolio">Portfolio</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($activePage ?? '') === 'pricing' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>pricing">Pricing</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($activePage ?? '') === 'faq' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>faq">FAQ</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($activePage ?? '') === 'blog' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>blog">Blog</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($activePage ?? '') === 'contact' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>contact">Contact</a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo CUSTOMER_URL; ?>"><i class="fas fa-user"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>logout">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>login">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn-signup" href="<?php echo SITE_URL; ?>register">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<br>
<br>