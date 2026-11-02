<?php
// /patchwebsite/customerpanel/dashboard.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

$db = getDB();
$userId = $_SESSION['user_id'];

$totalOrders = getCustomerOrderCount();
$pendingOrders = getCustomerPendingOrders();
$completedOrders = getCustomerCompletedOrders();

$stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

$pageTitle = 'Dashboard';
$currentPage = 'dashboard.php';
include 'includes/sidebar.php';
?>

<div class="customer-content">
    <!-- Welcome Header -->
    <div class="welcome-section">
        <div>
            <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</h1>
            <p class="text-muted">Here's what's happening with your orders</p>
        </div>
        <a href="new-order.php" class="btn-primary">
            <i class="fas fa-plus"></i> New Order
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-info">
                <span class="stat-number"><?php echo $totalOrders; ?></span>
                <span class="stat-label">Total Orders</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <span class="stat-number"><?php echo $pendingOrders; ?></span>
                <span class="stat-label">Pending Orders</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-number"><?php echo $completedOrders; ?></span>
                <span class="stat-label">Completed Orders</span>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="orders-section" id="orders">
        <div class="section-header">
            <h3><i class="fas fa-shopping-bag text-cyan"></i> Recent Orders</h3>
            <span class="order-count"><?php echo count($orders); ?> orders</span>
        </div>

        <div class="table-responsive-modern">
            <?php if (count($orders) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><span class="order-number"><?php echo $order['order_number']; ?></span></td>
                        <td><?php echo getServiceName($order['service_type']); ?></td>
                        <td><span class="status-badge <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                        <td><span class="payment-badge <?php echo $order['payment_status']; ?>"><?php echo ucfirst($order['payment_status']); ?></span></td>
                        <td><?php echo $order['price'] ? '$' . number_format($order['price'], 2) : '-'; ?></td>
                        <td><?php echo formatDate($order['created_at']); ?></td>
                        <td><a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-view">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h4>No orders yet</h4>
                <p>Place your first order today!</p>
                <a href="new-order.php" class="btn-primary">Start Order</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>