<?php
// /patchwebsite/adminpanel/customer-detail.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

$customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($customerId <= 0) {
    header('Location: customers.php');
    exit;
}

$db = getDB();

// Get customer details
$stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'customer'");
$stmt->execute([$customerId]);
$customer = $stmt->fetch();

if (!$customer) {
    header('Location: customers.php');
    exit;
}

// Get customer orders
$stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$customerId]);
$orders = $stmt->fetchAll();

// Get order count by status
$statusCounts = [];
$statuses = ['submitted', 'reviewing', 'quoted', 'in_production', 'completed', 'shipped', 'delivered'];
foreach ($statuses as $status) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = ?");
    $stmt->execute([$customerId, $status]);
    $statusCounts[$status] = $stmt->fetchColumn();
}

// Get total spent
$stmt = $db->prepare("SELECT COALESCE(SUM(price), 0) FROM orders WHERE user_id = ? AND payment_status = 'paid'");
$stmt->execute([$customerId]);
$totalSpent = $stmt->fetchColumn();

// Get total orders
$totalOrders = array_sum($statusCounts);

// Get recent comments
$stmt = $db->prepare("SELECT c.*, o.order_number FROM order_comments c JOIN orders o ON c.order_id = o.id WHERE o.user_id = ? ORDER BY c.created_at DESC LIMIT 10");
$stmt->execute([$customerId]);
$recentComments = $stmt->fetchAll();

$pageTitle = 'Customer: ' . $customer['name'];
$currentPage = 'customers.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1><i class="fas fa-user text-cyan"></i> <?php echo htmlspecialchars($customer['name']); ?></h1>
            <p>Customer details and order history</p>
        </div>
        <a href="customers.php" class="btn btn-outline-cyan"><i class="fas fa-arrow-left"></i> Back to Customers</a>
    </div>

    <!-- Customer Info Card -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(0, 240, 255, 0.1); color: var(--cyan);">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($customer['name']); ?></h3>
                    <p>Customer Name</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(0, 240, 255, 0.1); color: var(--cyan);">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3 style="font-size: 16px;"><?php echo htmlspecialchars($customer['email']); ?></h3>
                    <p>Email</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255, 0, 85, 0.1); color: var(--magenta);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalOrders; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255, 0, 85, 0.1); color: var(--magenta);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>$<?php echo number_format($totalSpent, 2); ?></h3>
                    <p>Total Spent</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Customer Details -->
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-id-card text-cyan"></i> Customer Details</h5>
                </div>
                <div class="admin-card-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
                    <p><strong>Email:</strong> <a href="mailto:<?php echo $customer['email']; ?>"><?php echo $customer['email']; ?></a></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone'] ?? 'Not provided'); ?></p>
                    <p><strong>Joined:</strong> <?php echo formatDateTime($customer['created_at']); ?></p>
                    <p><strong>Role:</strong> <span class="badge bg-info"><?php echo ucfirst($customer['role']); ?></span></p>
                    <?php if ($customer['updated_at']): ?>
                    <p><strong>Last Updated:</strong> <?php echo formatDateTime($customer['updated_at']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Status Summary -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-chart-pie text-cyan"></i> Order Status</h5>
                </div>
                <div class="admin-card-body">
                    <?php foreach ($statuses as $status): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted"><?php echo ucfirst(str_replace('_', ' ', $status)); ?></span>
                        <span class="badge <?php echo getStatusBadge($status); ?>"><?php echo $statusCounts[$status] ?? 0; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-shopping-cart text-cyan"></i> Order History</h5>
                    <span class="text-muted"><?php echo count($orders); ?> orders</span>
                </div>
                <div class="admin-card-body">
                    <?php if (count($orders) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
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
                                    <td><span class="text-cyan"><?php echo $order['order_number']; ?></span></td>
                                    <td><?php echo getServiceName($order['service_type']); ?></td>
                                    <td><span class="badge <?php echo getStatusBadge($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                    <td><span class="badge <?php echo getPaymentBadge($order['payment_status']); ?>"><?php echo ucfirst($order['payment_status']); ?></span></td>
                                    <td><?php echo $order['price'] ? '$' . number_format($order['price'], 2) : '-'; ?></td>
                                    <td><?php echo formatDate($order['created_at']); ?></td>
                                    <td><a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-cyan">View</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-center text-muted">No orders yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Comments -->
            <?php if (count($recentComments) > 0): ?>
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-comments text-cyan"></i> Recent Comments</h5>
                </div>
                <div class="admin-card-body">
                    <?php foreach ($recentComments as $comment): ?>
                    <div class="comment-item">
                        <strong>Order #<?php echo $comment['order_number']; ?></strong>
                        <small class="text-muted"><?php echo formatDateTime($comment['created_at']); ?></small>
                        <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>