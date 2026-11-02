<?php
// /patchwebsite/adminpanel/order-details.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

// ============================================
// FALLBACK FUNCTION IF mail.php NOT LOADED
// ============================================
if (!function_exists('sendProductionFilesReady')) {
    function sendProductionFilesReady($email, $name, $orderNumber) {
        // Simple fallback - just log
        error_log("Production files ready for Order #$orderNumber - Customer: $name ($email)");
        return true;
    }
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
    header('Location: orders.php');
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get order details
$stmt = $db->prepare("SELECT * FROM order_details WHERE order_id = ?");
$stmt->execute([$orderId]);
$orderDetails = $stmt->fetchAll();

// Get artwork files (customer uploaded)
$stmt = $db->prepare("SELECT * FROM order_files WHERE order_id = ? AND file_type = 'artwork'");
$stmt->execute([$orderId]);
$artworkFiles = $stmt->fetchAll();

// Get production files (admin uploaded)
$stmt = $db->prepare("SELECT * FROM order_files WHERE order_id = ? AND file_type = 'production'");
$stmt->execute([$orderId]);
$productionFiles = $stmt->fetchAll();

// Get order comments
$stmt = $db->prepare("SELECT c.*, u.name as user_name FROM order_comments c JOIN users u ON c.user_id = u.id WHERE c.order_id = ? ORDER BY c.created_at ASC");
$stmt->execute([$orderId]);
$comments = $stmt->fetchAll();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $newStatus = sanitize($_POST['status']);
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        
        $stmt = $db->prepare("INSERT INTO order_status_history (order_id, status, notes) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $newStatus, 'Status updated by admin']);
        
        header('Location: order-details.php?id=' . $orderId);
        exit;
    }
    
    if ($_POST['action'] === 'add_comment') {
        $comment = sanitize($_POST['comment']);
        $stmt = $db->prepare("INSERT INTO order_comments (order_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $_SESSION['user_id'], $comment]);
        header('Location: order-details.php?id=' . $orderId);
        exit;
    }
    
    if ($_POST['action'] === 'add_payment_link') {
        $paymentLink = sanitize($_POST['payment_link']);
        $stmt = $db->prepare("UPDATE orders SET payment_link = ? WHERE id = ?");
        $stmt->execute([$paymentLink, $orderId]);
        header('Location: order-details.php?id=' . $orderId);
        exit;
    }
    
    if ($_POST['action'] === 'mark_paid') {
        $stmt = $db->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?");
        $stmt->execute([$orderId]);
        header('Location: order-details.php?id=' . $orderId);
        exit;
    }
    
    if ($_POST['action'] === 'update_price') {
        $price = (float)$_POST['price'];
        $stmt = $db->prepare("UPDATE orders SET price = ? WHERE id = ?");
        $stmt->execute([$price, $orderId]);
        header('Location: order-details.php?id=' . $orderId);
        exit;
    }
    
    if ($_POST['action'] === 'update_tracking') {
        $tracking = sanitize($_POST['tracking_number']);
        $stmt = $db->prepare("UPDATE orders SET tracking_number = ? WHERE id = ?");
        $stmt->execute([$tracking, $orderId]);
        header('Location: order-details.php?id=' . $orderId);
        exit;
    }
    
    if ($_POST['action'] === 'delete_file') {
        $fileId = (int)$_POST['file_id'];
        $stmt = $db->prepare("SELECT file_path FROM order_files WHERE id = ? AND order_id = ?");
        $stmt->execute([$fileId, $orderId]);
        $file = $stmt->fetch();
        if ($file) {
            $fullPath = __DIR__ . '/../' . $file['file_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            $stmt = $db->prepare("DELETE FROM order_files WHERE id = ?");
            $stmt->execute([$fileId]);
        }
        header('Location: order-details.php?id=' . $orderId);
        exit;
    }
}

// Handle multiple file uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['production_files'])) {
    $files = $_FILES['production_files'];
    $uploadedCount = 0;
    
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK && $files['size'][$i] > 0) {
            $uploadDir = UPLOAD_PATH_PRODUCTION . $orderId . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
            $newName = uniqid() . '_' . time() . '_' . $i . '.' . $ext;
            $targetPath = $uploadDir . $newName;
            
            if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                $stmt = $db->prepare("INSERT INTO order_files (order_id, file_name, file_path, file_type) VALUES (?, ?, ?, 'production')");
                $stmt->execute([$orderId, $files['name'][$i], 'uploads/production/' . $orderId . '/' . $newName]);
                $uploadedCount++;
            }
        }
    }
    
    if ($uploadedCount > 0) {
        // Send notification email
        $stmt = $db->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->execute([$order['user_id']]);
        $customer = $stmt->fetch();
        if ($customer) {
            sendProductionFilesReady($customer['email'], $customer['name'], $order['order_number']);
        }
    }
    
    header('Location: order-details.php?id=' . $orderId);
    exit;
}

$statuses = ['submitted', 'reviewing', 'quoted', 'in_production', 'completed', 'shipped', 'delivered'];

$pageTitle = 'Order #' . $order['order_number'];
$currentPage = 'orders.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Order #<?php echo $order['order_number']; ?></h1>
            <p>Manage this order</p>
        </div>
        <a href="orders.php" class="btn btn-outline-cyan"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>

    <div class="row g-4">
        <!-- Left Column: Order Info, Details, Artwork, Comments -->
        <div class="col-lg-8">
            <!-- Order Information -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-info-circle text-cyan"></i> Order Information</h5>
                </div>
                <div class="admin-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>Order Number:</strong> <span class="text-cyan"><?php echo $order['order_number']; ?></span></p>
                            <p><strong>Service:</strong> <?php echo getServiceName($order['service_type']); ?></p>
                            <p><strong>Status:</strong> <span class="badge <?php echo getStatusBadge($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></p>
                            <p><strong>Payment Status:</strong> <span class="badge <?php echo getPaymentBadge($order['payment_status']); ?>"><?php echo ucfirst($order['payment_status']); ?></span></p>
                            <?php if ($order['patch_type']): ?>
                            <p><strong>Patch Type:</strong> <?php echo ucfirst($order['patch_type']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><strong>Email:</strong> <a href="mailto:<?php echo $order['customer_email']; ?>"><?php echo $order['customer_email']; ?></a></p>
                            <?php if ($order['phone']): ?>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            <?php endif; ?>
                            <p><strong>Created:</strong> <?php echo formatDateTime($order['created_at']); ?></p>
                            <?php if ($order['price']): ?>
                            <p><strong>Price:</strong> $<?php echo number_format($order['price'], 2); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($order['shipping_address']): ?>
                    <hr>
                    <h6><i class="fas fa-shipping-fast text-cyan"></i> Shipping Address</h6>
                    <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($order['tracking_number']): ?>
                    <p><strong>Tracking Number:</strong> <?php echo htmlspecialchars($order['tracking_number']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Details -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-list text-cyan"></i> Order Details</h5>
                </div>
                <div class="admin-card-body">
                    <?php if (count($orderDetails) > 0): ?>
                    <div class="row g-2">
                        <?php foreach ($orderDetails as $detail): ?>
                        <div class="col-md-6">
                            <strong><?php echo ucfirst(str_replace('_', ' ', $detail['field_name'])); ?>:</strong>
                            <span><?php echo htmlspecialchars($detail['field_value']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No additional details.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Artwork Files (Customer Uploaded) -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-upload text-cyan"></i> Artwork Files</h5>
                    <span class="badge bg-info"><?php echo count($artworkFiles); ?> files</span>
                </div>
                <div class="admin-card-body">
                    <?php if (count($artworkFiles) > 0): ?>
                    <div class="d-flex justify-content-end mb-2">
                        <a href="download-all.php?order_id=<?php echo $orderId; ?>&type=artwork" class="btn btn-sm btn-outline-cyan">
                            <i class="fas fa-download"></i> Download All
                        </a>
                    </div>
                    <div class="row g-2">
                        <?php foreach ($artworkFiles as $file): ?>
                        <div class="col-md-4">
                            <div class="file-item">
                                <i class="fas fa-file-image"></i>
                                <span><?php echo htmlspecialchars($file['file_name']); ?></span>
                                <div class="file-actions">
                                    <a href="<?php echo SITE_URL . $file['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-cyan" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No artwork files uploaded.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Comments -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-comments text-cyan"></i> Comments</h5>
                </div>
                <div class="admin-card-body">
                    <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                    <div class="comment-item <?php echo $comment['user_id'] == $_SESSION['user_id'] ? 'admin' : 'customer'; ?>">
                        <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                        <small class="text-muted"><?php echo formatDateTime($comment['created_at']); ?></small>
                        <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="text-muted">No comments yet.</p>
                    <?php endif; ?>
                    
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="action" value="add_comment">
                        <div class="row g-2">
                            <div class="col-10">
                                <input type="text" name="comment" class="form-control" placeholder="Add a comment..." required>
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-primary-cyan w-100">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Sidebar: Actions + Production Files -->
        <div class="col-lg-4">
            <!-- Update Status -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-edit text-cyan"></i> Update Status</h5>
                </div>
                <div class="admin-card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_status">
                        <select name="status" class="form-select mb-2">
                            <?php foreach ($statuses as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $s)); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary-cyan w-100">Update Status</button>
                    </form>
                </div>
            </div>

            <!-- Payment -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-credit-card text-cyan"></i> Payment</h5>
                </div>
                <div class="admin-card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_payment_link">
                        <input type="url" name="payment_link" class="form-control mb-2" placeholder="Payment link" value="<?php echo htmlspecialchars($order['payment_link'] ?? ''); ?>">
                        <button type="submit" class="btn btn-outline-cyan w-100 mb-2">Add Payment Link</button>
                    </form>
                    
                    <?php if ($order['payment_status'] !== 'paid'): ?>
                    <form method="POST">
                        <input type="hidden" name="action" value="mark_paid">
                        <button type="submit" class="btn btn-success w-100">Mark as Paid</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pricing -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-tag text-cyan"></i> Pricing</h5>
                </div>
                <div class="admin-card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_price">
                        <div class="input-group mb-2">
                            <span class="input-group-text">$</span>
                            <input type="number" name="price" class="form-control" step="0.01" placeholder="0.00" value="<?php echo $order['price'] ?? ''; ?>">
                        </div>
                        <button type="submit" class="btn btn-outline-cyan w-100">Update Price</button>
                    </form>
                </div>
            </div>

            <!-- Tracking -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-box text-cyan"></i> Tracking</h5>
                </div>
                <div class="admin-card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_tracking">
                        <input type="text" name="tracking_number" class="form-control mb-2" placeholder="Tracking Number" value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>">
                        <button type="submit" class="btn btn-outline-cyan w-100">Update Tracking</button>
                    </form>
                </div>
            </div>

            <!-- Production Files (Admin Uploaded) - In Sidebar -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-file-export text-cyan"></i> Production Files</h5>
                    <span class="badge bg-success"><?php echo count($productionFiles); ?> files</span>
                </div>
                <div class="admin-card-body">
                    <!-- Upload Multiple Production Files -->
                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                        <div class="mb-2">
                            <label class="form-label text-muted small">Upload multiple files (Ctrl+Click to select multiple)</label>
                            <input type="file" name="production_files[]" class="form-control" multiple required>
                        </div>
                        <button type="submit" class="btn btn-primary-cyan w-100">
                            <i class="fas fa-upload"></i> Upload Files
                        </button>
                    </form>

                    <?php if (count($productionFiles) > 0): ?>
                    <div class="d-flex justify-content-end mb-2">
                        <a href="download-all.php?order_id=<?php echo $orderId; ?>&type=production" class="btn btn-sm btn-outline-cyan">
                            <i class="fas fa-download"></i> Download All
                        </a>
                    </div>
                    <?php foreach ($productionFiles as $file): ?>
                    <div class="file-item production mb-2">
                        <i class="fas fa-file-code"></i>
                        <span><?php echo htmlspecialchars($file['file_name']); ?></span>
                        <div class="file-actions">
                            <a href="<?php echo SITE_URL . $file['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-cyan" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this file?')">
                                <input type="hidden" name="action" value="delete_file">
                                <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="text-muted text-center">No production files uploaded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>