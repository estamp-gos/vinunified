<?php
// /patchwebsite/adminpanel/dashboard.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

// ============================================
// FALLBACK FUNCTIONS (if not loaded from functions.php)
// ============================================
if (!function_exists('getServiceName')) {
    function getServiceName($type) {
        $names = [
            'digitizing' => 'Embroidery Digitizing',
            'vector' => 'Vector Tracing',
            'character' => 'Character Art',
            'patches' => 'Custom Patches'
        ];
        return $names[$type] ?? ucfirst($type);
    }
}

if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status) {
        $badges = [
            'submitted' => 'bg-secondary',
            'reviewing' => 'bg-warning text-dark',
            'quoted' => 'bg-info text-dark',
            'in_production' => 'bg-primary',
            'completed' => 'bg-success',
            'shipped' => 'bg-purple',
            'delivered' => 'bg-success'
        ];
        return $badges[$status] ?? 'bg-secondary';
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date) {
        return date('M d, Y', strtotime($date));
    }
}

// ============================================
// GET STATISTICS
// ============================================
$db = getDB();

$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status IN ('submitted', 'reviewing')")->fetchColumn();
$totalCustomers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalRevenue = $db->query("SELECT COALESCE(SUM(price), 0) FROM orders WHERE payment_status = 'paid'")->fetchColumn();

// Get orders by status
$statusCounts = [];
$statuses = ['submitted', 'reviewing', 'quoted', 'in_production', 'completed', 'shipped', 'delivered'];
foreach ($statuses as $status) {
    $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE status = '$status'");
    $statusCounts[$status] = $stmt->fetchColumn();
}

// Recent orders
$recentOrders = $db->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

// Recent customers
$recentCustomers = $db->query("SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC LIMIT 5")->fetchAll();

$pageTitle = 'Dashboard';
$currentPage = 'dashboard.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(0, 240, 255, 0.1); color: var(--cyan);">
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
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pendingOrders; ?></h3>
                    <p>Pending Orders</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(0, 240, 255, 0.1); color: var(--cyan);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalCustomers; ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255, 0, 85, 0.1); color: var(--magenta);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>$<?php echo number_format($totalRevenue, 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-shopping-cart text-cyan"></i> Recent Orders</h5>
                    <a href="orders.php" class="btn btn-sm btn-outline-cyan">View All</a>
                </div>
                <div class="admin-card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($recentOrders) > 0): ?>
                                <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><span class="text-cyan"><?php echo $order['order_number']; ?></span></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo getServiceName($order['service_type']); ?></td>
                                    <td><span class="badge <?php echo getStatusBadge($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                    <td><?php echo $order['price'] ? '$' . number_format($order['price'], 2) : '-'; ?></td>
                                    <td><a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-cyan">View</a></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr><td colspan="6" class="text-center text-muted">No orders yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Customers -->
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-users text-cyan"></i> New Customers</h5>
                    <a href="customers.php" class="btn btn-sm btn-outline-cyan">View All</a>
                </div>
                <div class="admin-card-body">
                    <?php if (count($recentCustomers) > 0): ?>
                    <?php foreach ($recentCustomers as $customer): ?>
                    <div class="customer-item">
                        <div class="customer-avatar">
                            <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
                        </div>
                        <div class="customer-info">
                            <strong><?php echo htmlspecialchars($customer['name']); ?></strong>
                            <small><?php echo htmlspecialchars($customer['email']); ?></small>
                        </div>
                        <small class="text-muted ms-auto"><?php echo formatDate($customer['created_at']); ?></small>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="text-center text-muted">No customers yet.</p>
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
    </div>
</div>

<?php include 'includes/footer.php'; ?>