<?php
// /patchwebsite/customerpanel/includes/sidebar.php

$currentPage = basename($_SERVER['PHP_SELF']);
$customerUser = getCustomerUser();
?>
<aside class="customer-sidebar">
    <!-- Brand Logo -->
    <div class="sidebar-brand">
        <img src="<?php echo SITE_URL; ?>assets/images/logo-horizontal.png" alt="PunchyArts" height="35">
    </div>

    <!-- User Profile -->
    <div class="sidebar-profile">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($customerUser['name'] ?? 'U', 0, 1)); ?>
            <span class="online-status"></span>
        </div>
        <div class="profile-info">
            <h6><?php echo htmlspecialchars($customerUser['name'] ?? 'User'); ?></h6>
            <span><?php echo htmlspecialchars($customerUser['email'] ?? ''); ?></span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>customerpanel/dashboard.php">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo $currentPage === 'new-order.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>customerpanel/new-order.php">
                    <i class="fas fa-plus-circle"></i>
                    <span>New Order</span>
                </a>
            </li>
            <li class="<?php echo $currentPage === 'order-details.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>customerpanel/dashboard.php#orders">
                    <i class="fas fa-shopping-bag"></i>
                    <span>My Orders</span>
                </a>
            </li>
            <li class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>customerpanel/profile.php">
                    <i class="fas fa-user-cog"></i>
                    <span>Profile Settings</span>
                </a>
            </li>
            <li class="logout-li">
                <a href="<?php echo SITE_URL; ?>logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Footer Info -->
    <div class="sidebar-footer">
        <small>&copy; <?php echo date('Y'); ?> PunchyArts</small>
        <small>v1.0</small>
    </div>
</aside>

<div class="customer-content">