<?php
// /patchwebsite/customerpanel/order-complete.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

$orderNumber = isset($_GET['order']) ? sanitize($_GET['order']) : '';

$pageTitle = 'Order Complete';
$currentPage = 'order-complete.php';
include 'includes/sidebar.php';
?>

<div class="customer-content">
    <div class="customer-header text-center">
        <div style="font-size: 80px; color: #00F0FF; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Order Placed Successfully! 🎉</h1>
        <p class="text-muted">Thank you for your order. We will review it and get back to you shortly.</p>
        <?php if ($orderNumber): ?>
        <p class="text-cyan">Order Number: <strong><?php echo $orderNumber; ?></strong></p>
        <?php endif; ?>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="admin-card">
                <div class="admin-card-body text-center">
                    <h5>What's Next?</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-check text-cyan"></i> We will review your order within 24 hours</li>
                        <li><i class="fas fa-check text-cyan"></i> You will receive a confirmation email</li>
                        <li><i class="fas fa-check text-cyan"></i> Check your order status from the dashboard</li>
                    </ul>
                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-3">
                        <a href="dashboard.php" class="btn btn-primary-cyan">
                            <i class="fas fa-home"></i> Go to Dashboard
                        </a>
                        <a href="new-order.php" class="btn btn-outline-cyan">
                            <i class="fas fa-plus-circle"></i> Place Another Order
                        </a>
                        <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-cyan">
                            <i class="fas fa-globe"></i> Visit Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>