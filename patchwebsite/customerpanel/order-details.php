<?php
// /patchwebsite/customerpanel/order-details.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/upload.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];

if ($orderId <= 0) {
    header('Location: dashboard.php');
    exit;
}

$db = getDB();

// Get order - ensure it belongs to this customer
$stmt = $db->prepare("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: dashboard.php');
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

// Add comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $comment = sanitize($_POST['comment']);
    $stmt = $db->prepare("INSERT INTO order_comments (order_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$orderId, $userId, $comment]);
    header('Location: order-details.php?id=' . $orderId);
    exit;
}

// Handle customer file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_artwork') {
    if (isset($_FILES['new_artwork']) && !empty($_FILES['new_artwork']['name'][0])) {
        $result = saveOrderArtwork($_FILES['new_artwork'], $orderId);
        if ($result['success']) {
            $stmt = $db->prepare("INSERT INTO order_comments (order_id, user_id, comment) VALUES (?, ?, ?)");
            $stmt->execute([$orderId, $userId, 'Added new artwork file(s)']);
            setFlash('success', 'Files uploaded successfully!');
        } else {
            setFlash('error', 'File upload failed: ' . ($result['error'] ?? 'Unknown error'));
        }
    }
    header('Location: order-details.php?id=' . $orderId);
    exit;
}

// Helper function to get file icon
function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        'jpg' => 'fa-file-image',
        'jpeg' => 'fa-file-image',
        'png' => 'fa-file-image',
        'gif' => 'fa-file-image',
        'webp' => 'fa-file-image',
        'pdf' => 'fa-file-pdf',
        'doc' => 'fa-file-word',
        'docx' => 'fa-file-word',
        'xls' => 'fa-file-excel',
        'xlsx' => 'fa-file-excel',
        'zip' => 'fa-file-archive',
        'rar' => 'fa-file-archive',
        'ai' => 'fa-file',
        'eps' => 'fa-file',
        'psd' => 'fa-file',
        'dst' => 'fa-file',
        'pes' => 'fa-file',
        'emb' => 'fa-file',
        'cnd' => 'fa-file',
        'txt' => 'fa-file-alt',
        'csv' => 'fa-file-csv'
    ];
    return $icons[$ext] ?? 'fa-file';
}

// Helper function to check if file is an image
function isImageFile($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}

// Helper function to truncate filename
function truncateFilename($filename, $maxLength = 20) {
    if (strlen($filename) <= $maxLength) {
        return $filename;
    }
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $truncated = substr($name, 0, $maxLength - strlen($ext) - 4) . '...';
    return $truncated . '.' . $ext;
}

$pageTitle = 'Order #' . $order['order_number'];
$currentPage = 'order-details.php';
include 'includes/sidebar.php';
?>

<div class="customer-content">
    <div class="customer-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Order #<?php echo $order['order_number']; ?></h1>
            <p>View your order details</p>
        </div>
        <a href="dashboard.php#orders" class="btn btn-outline-cyan"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>

    <?php displayFlash(); ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Order Info -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-info-circle text-cyan"></i> Order Information</h5>
                </div>
                <div class="admin-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>Order Number:</strong> <span class="text-cyan"><?php echo $order['order_number']; ?></span></p>
                            <p><strong>Service:</strong> <?php echo $order['service_type'] ? getServiceName($order['service_type']) : 'Not specified'; ?></p>
                            <p><strong>Status:</strong> <span class="badge <?php echo getStatusBadge($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></p>
                            <p><strong>Payment Status:</strong> <span class="badge <?php echo getPaymentBadge($order['payment_status']); ?>"><?php echo ucfirst($order['payment_status']); ?></span></p>
                            <?php if ($order['patch_type']): ?>
                            <p><strong>Patch Type:</strong> <?php echo ucfirst($order['patch_type']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Created:</strong> <?php echo formatDateTime($order['created_at']); ?></p>
                            <?php if ($order['price']): ?>
                            <p><strong>Price:</strong> $<?php echo number_format($order['price'], 2); ?></p>
                            <?php endif; ?>
                            <?php if ($order['payment_link']): ?>
                            <p><strong>Payment Link:</strong> <a href="<?php echo $order['payment_link']; ?>" target="_blank" class="text-cyan">Pay Now</a></p>
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
                <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h5><i class="fas fa-upload text-cyan"></i> My Artwork Files</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-info"><?php echo count($artworkFiles); ?> files</span>
                        <?php if (count($artworkFiles) > 0): ?>
                        <a href="download-all.php?order_id=<?php echo $orderId; ?>&type=artwork" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Download All
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="admin-card-body">
                    <form method="POST" enctype="multipart/form-data" class="mb-3">
                        <input type="hidden" name="action" value="upload_artwork">
                        <div class="row g-2">
                            <div class="col-8">
                                <input type="file" name="new_artwork[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.ai,.eps,.psd,.dst,.pes,.emb,.cnd,.zip,.rar">
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary-cyan w-100">
                                    <i class="fas fa-upload"></i> Upload Files
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Max 50MB per file. Supported: JPG, PNG, PDF, AI, EPS, PSD, DST, PES, EMB, CND, ZIP, RAR</small>
                    </form>

                    <?php if (count($artworkFiles) > 0): ?>
                    <div class="row g-3">
                        <?php foreach ($artworkFiles as $file): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="file-card">
                                <div class="file-preview">
                                    <?php if (isImageFile($file['file_name'])): ?>
                                        <img src="<?php echo SITE_URL . $file['file_path']; ?>" alt="<?php echo htmlspecialchars($file['file_name']); ?>" class="file-thumbnail">
                                    <?php else: ?>
                                        <div class="file-icon-wrapper">
                                            <i class="fas <?php echo getFileIcon($file['file_name']); ?> file-icon-display"></i>
                                            <span class="file-ext"><?php echo strtoupper(pathinfo($file['file_name'], PATHINFO_EXTENSION)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="file-info">
                                    <span class="file-name" title="<?php echo htmlspecialchars($file['file_name']); ?>">
                                        <?php echo htmlspecialchars(truncateFilename($file['file_name'], 18)); ?>
                                    </span>
                                    <a href="<?php echo SITE_URL . $file['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-cyan download-btn" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No artwork files uploaded yet. Use the form above to upload.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Production Files (Admin Uploaded) -->
            <div class="admin-card mt-4">
                <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h5><i class="fas fa-file-export text-cyan"></i> Production Files</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success"><?php echo count($productionFiles); ?> files</span>
                        <?php if (count($productionFiles) > 0): ?>
                        <a href="download-all.php?order_id=<?php echo $orderId; ?>&type=production" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Download All
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="admin-card-body">
                    <?php if (count($productionFiles) > 0): ?>
                    <div class="row g-3">
                        <?php foreach ($productionFiles as $file): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="file-card production">
                                <div class="file-preview">
                                    <?php if (isImageFile($file['file_name'])): ?>
                                        <img src="<?php echo SITE_URL . $file['file_path']; ?>" alt="<?php echo htmlspecialchars($file['file_name']); ?>" class="file-thumbnail">
                                    <?php else: ?>
                                        <div class="file-icon-wrapper">
                                            <i class="fas <?php echo getFileIcon($file['file_name']); ?> file-icon-display"></i>
                                            <span class="file-ext"><?php echo strtoupper(pathinfo($file['file_name'], PATHINFO_EXTENSION)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="file-info">
                                    <span class="file-name" title="<?php echo htmlspecialchars($file['file_name']); ?>">
                                        <?php echo htmlspecialchars(truncateFilename($file['file_name'], 18)); ?>
                                    </span>
                                    <a href="<?php echo SITE_URL . $file['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-cyan download-btn" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No production files uploaded yet.</p>
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
                    <div class="comment-item <?php echo $comment['user_id'] == $userId ? 'customer' : 'admin'; ?>">
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

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-clock text-cyan"></i> Order Timeline</h5>
                </div>
                <div class="admin-card-body">
                    <ul class="timeline">
                        <li class="timeline-item <?php echo $order['status'] === 'submitted' ? 'active' : 'completed'; ?>">
                            <span class="timeline-icon"><i class="fas fa-check-circle"></i></span>
                            <span class="timeline-text">Submitted</span>
                        </li>
                        <li class="timeline-item <?php echo in_array($order['status'], ['reviewing', 'quoted', 'in_production', 'completed', 'shipped', 'delivered']) ? 'completed' : ''; ?>">
                            <span class="timeline-icon"><i class="fas fa-search"></i></span>
                            <span class="timeline-text">Under Review</span>
                        </li>
                        <li class="timeline-item <?php echo in_array($order['status'], ['quoted', 'in_production', 'completed', 'shipped', 'delivered']) ? 'completed' : ''; ?>">
                            <span class="timeline-icon"><i class="fas fa-tag"></i></span>
                            <span class="timeline-text">Quoted</span>
                        </li>
                        <li class="timeline-item <?php echo in_array($order['status'], ['in_production', 'completed', 'shipped', 'delivered']) ? 'completed' : ''; ?>">
                            <span class="timeline-icon"><i class="fas fa-cogs"></i></span>
                            <span class="timeline-text">In Production</span>
                        </li>
                        <li class="timeline-item <?php echo in_array($order['status'], ['completed', 'shipped', 'delivered']) ? 'completed' : ''; ?>">
                            <span class="timeline-icon"><i class="fas fa-check-double"></i></span>
                            <span class="timeline-text">Completed</span>
                        </li>
                        <?php if ($order['service_type'] === 'patches' && $order['patch_type'] === 'physical'): ?>
                        <li class="timeline-item <?php echo in_array($order['status'], ['shipped', 'delivered']) ? 'completed' : ''; ?>">
                            <span class="timeline-icon"><i class="fas fa-shipping-fast"></i></span>
                            <span class="timeline-text">Shipped</span>
                        </li>
                        <li class="timeline-item <?php echo $order['status'] === 'delivered' ? 'completed' : ''; ?>">
                            <span class="timeline-icon"><i class="fas fa-home"></i></span>
                            <span class="timeline-text">Delivered</span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Status History -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5><i class="fas fa-history text-cyan"></i> Status History</h5>
                </div>
                <div class="admin-card-body">
                    <?php
                    $stmt = $db->prepare("SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at DESC");
                    $stmt->execute([$orderId]);
                    $history = $stmt->fetchAll();
                    ?>
                    <?php if (count($history) > 0): ?>
                    <?php foreach ($history as $h): ?>
                    <div class="history-item">
                        <span class="badge <?php echo getStatusBadge($h['status']); ?>"><?php echo ucfirst($h['status']); ?></span>
                        <small class="text-muted ms-2"><?php echo formatDateTime($h['created_at']); ?></small>
                        <?php if ($h['notes']): ?>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($h['notes']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="text-muted">No history available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* File card styles - important */
.file-card {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.file-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.file-card.production {
    border-color: #28a745;
    background: #f0fff4;
}

.file-preview {
    width: 100%;
    height: 180px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    border-bottom: 1px solid #e9ecef;
}

.file-thumbnail {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

.file-icon-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    background: #f8f9fa;
}

.file-icon-display {
    font-size: 60px;
    color: #6c757d;
}

.file-ext {
    font-size: 12px;
    font-weight: bold;
    color: #6c757d;
    margin-top: 5px;
    background: #e9ecef;
    padding: 2px 10px;
    border-radius: 4px;
}

.file-info {
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    min-height: 40px;
    background: #ffffff;
}

.file-name {
    font-size: 13px;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
    min-width: 0;
}

.download-btn {
    flex-shrink: 0;
    padding: 2px 8px;
    font-size: 12px;
    border-radius: 4px;
}

.download-btn i {
    font-size: 12px;
}

/* Responsive */
@media (max-width: 768px) {
    .file-preview {
        height: 140px;
    }
    .file-icon-display {
        font-size: 40px;
    }
    .file-name {
        font-size: 11px;
    }
}

@media (max-width: 576px) {
    .file-preview {
        height: 120px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>