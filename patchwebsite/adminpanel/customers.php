<?php
// /patchwebsite/adminpanel/customers.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

$db = getDB();

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$sql = "SELECT * FROM users WHERE role = 'customer'";
$params = [];

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

$pageTitle = 'Customers';
$currentPage = 'customers.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Customers</h1>
            <p>Manage all registered customers</p>
        </div>
        <span class="text-muted">Total: <?php echo count($customers); ?> customers</span>
    </div>

    <!-- Search -->
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary-cyan">Search</button>
                </div>
                <div class="col-md-2">
                    <a href="customers.php" class="btn btn-outline-cyan">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="admin-card">
        <div class="admin-card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Orders</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($customers) > 0): ?>
                        <?php foreach ($customers as $index => $customer): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($customer['name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
                            <td><?php echo formatDate($customer['created_at']); ?></td>
                            <td>
                                <?php
                                $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
                                $stmt->execute([$customer['id']]);
                                echo $stmt->fetchColumn();
                                ?>
                            </td>
                            <td>
                                <a href="customer-detail.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-cyan">
                                    <i class="fas fa-user"></i> View Details
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No customers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>