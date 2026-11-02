<?php
// /patchwebsite/adminpanel/orders.php
require_once 'includes/config.php';
require_once 'includes/header.php';

$db = getDB();

// Filters
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$sql = "SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id WHERE 1=1";
$params = [];

if (!empty($status)) {
    $sql .= " AND o.status = ?";
    $params[] = $status;
}

if (!empty($search)) {
    $sql .= " AND (o.order_number LIKE ? OR u.name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY o.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$statuses = ['submitted', 'reviewing', 'quoted', 'in_production', 'completed', 'shipped', 'delivered'];

$pageTitle = 'Orders';
$currentPage = 'orders.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Orders</h1>
            <p>Manage all customer orders</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <?php foreach ($statuses as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $status === $s ? 'selected' : ''; ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $s)); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Order #, Customer, Email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-cyan me-2">Filter</button>
                    <a href="orders.php" class="btn btn-outline-cyan">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="admin-card">
        <div class="admin-card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><span class="text-cyan"><?php echo $order['order_number']; ?></span></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo getServiceName($order['service_type']); ?></td>
                            <td><span class="badge <?php echo getStatusBadge($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            <td><span class="badge <?php echo getPaymentBadge($order['payment_status']); ?>"><?php echo ucfirst($order['payment_status']); ?></span></td>
                            <td><?php echo $order['price'] ? CURRENCY_SYMBOL . number_format($order['price'], 2) : '-'; ?></td>
                            <td><?php echo formatDate($order['created_at']); ?></td>
                            <td><a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-cyan">View</a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>