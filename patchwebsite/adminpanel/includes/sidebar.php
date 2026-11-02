<?php
// /patchwebsite/adminpanel/includes/sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-sidebar">
    <div class="sidebar-brand">
        <img src="<?php echo SITE_URL; ?>assets/images/logo-horizontal.png" alt="PunchyArts" height="40">
    </div>
    <ul class="sidebar-nav">
        <li class="<?php echo $currentPage === 'dashboard.php' || $currentPage === 'index.php' ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>dashboard.php">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
        </li>
        <li class="<?php echo $currentPage === 'orders.php' || $currentPage === 'order-details.php' ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>orders.php">
                <i class="fas fa-shopping-cart"></i> Orders
                <span class="badge" id="orderBadge">0</span>
            </a>
        </li>
        <li class="<?php echo $currentPage === 'customers.php' ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>customers.php">
                <i class="fas fa-users"></i> Customers
            </a>
        </li>
        <li class="<?php echo $currentPage === 'portfolio.php' ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>portfolio.php">
                <i class="fas fa-images"></i> Portfolio
            </a>
        </li>
        <li class="<?php echo $currentPage === 'blog.php' ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>blog.php">
                <i class="fas fa-newspaper"></i> Blog
            </a>
        </li>
        <li class="<?php echo $currentPage === 'faq.php' ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>faq.php">
                <i class="fas fa-question-circle"></i> FAQ
            </a>
        </li>
        <li class="<?php echo $currentPage === 'pricing.php' ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>pricing.php">
                <i class="fas fa-tag"></i> Pricing
            </a>
        </li>
       
        <li class="<?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
            <a href="<?php echo ADMIN_URL; ?>settings.php">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>