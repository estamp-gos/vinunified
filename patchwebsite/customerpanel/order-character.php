<?php
// /patchwebsite/customerpanel/order-character.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../includes/mail.php';

$db = getDB();
$userId = $_SESSION['user_id'];
$error = '';
$serviceType = 'character';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $patchType = null;
    $instructions = isset($_POST['instructions']) ? sanitize($_POST['instructions']) : '';
    
    $orderNumber = generateOrderNumber();
    $stmt = $db->prepare("INSERT INTO orders (order_number, user_id, service_type, patch_type, status, admin_notes) VALUES (?, ?, ?, ?, 'submitted', ?)");
    $stmt->execute([$orderNumber, $userId, $serviceType, $patchType, $instructions]);
    $orderId = $db->lastInsertId();
    
    $detailFields = [
        'custom_type', 'size', 'intended_use', 'style_reference'
    ];
    
    foreach ($detailFields as $field) {
        if (isset($_POST[$field]) && !empty($_POST[$field])) {
            $value = sanitize($_POST[$field]);
            $stmt = $db->prepare("INSERT INTO order_details (order_id, field_name, field_value) VALUES (?, ?, ?)");
            $stmt->execute([$orderId, $field, $value]);
        }
    }
    
    if (isset($_FILES['artwork_files']) && !empty($_FILES['artwork_files']['name'][0])) {
        saveOrderArtwork($_FILES['artwork_files'], $orderId);
    }
    
    $stmt = $db->prepare("INSERT INTO order_status_history (order_id, status, notes) VALUES (?, 'submitted', ?)");
    $stmt->execute([$orderId, 'Order submitted by customer']);
    
    $customer = getCustomerUser();
    sendOrderConfirmation($customer['email'], $customer['name'], $orderNumber, $serviceType);
    sendOrderConfirmationAdmin($customer['email'], $customer['name'], $orderNumber, $serviceType, $orderId);
    
    header('Location: order-complete.php?order=' . $orderNumber);
    exit;
}

$pageTitle = 'Character Art Order';
$currentPage = 'new-order.php';
include 'includes/sidebar.php';
?>

<div class="customer-content">
    <div class="customer-header">
        <h1>Character & Mascot Art</h1>
        <p>Fill in the details for your character art order</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="admin-card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="submit_order" value="1">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Custom Type</label>
                        <select name="custom_type" class="form-select">
                            <option value="2D Mascot Logo">2D Mascot Logo</option>
                            <option value="3D Mascot Logo">3D Mascot Logo</option>
                            <option value="Character Illustration">Character Illustration</option>
                            <option value="Gaming Art">Gaming Art</option>
                            <option value="Anime Merch Art">Anime Merch Art</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Size</label>
                        <input type="text" name="size" class="form-control" placeholder="1000x1000px">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Intended Use</label>
                        <input type="text" name="intended_use" class="form-control" placeholder="Website Logo, Merchandise, etc.">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Style Reference</label>
                        <input type="text" name="style_reference" class="form-control" placeholder="Cartoon, Realistic, etc.">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Special Instructions</label>
                        <textarea name="instructions" class="form-control" rows="3" placeholder="Any special requirements..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Upload Artwork/Design</label>
                        <input type="file" name="artwork_files[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.ai,.eps,.psd,.dst,.pes,.emb,.cnd,.zip,.rar">
                        <small class="text-muted">Max 50MB per file. Supported: JPG, PNG, PDF, AI, EPS, PSD, DST, PES, EMB, CND, ZIP, RAR</small>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn-hero-primary w-100">
                            <i class="fas fa-paper-plane"></i> Submit Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>