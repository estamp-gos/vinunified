<?php
// /patchwebsite/customerpanel/order-patches.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../includes/mail.php';

$db = getDB();
$userId = $_SESSION['user_id'];
$error = '';
$serviceType = 'patches';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $patchType = isset($_POST['patch_type']) ? sanitize($_POST['patch_type']) : 'digital';
    $instructions = isset($_POST['instructions']) ? sanitize($_POST['instructions']) : '';
    
    $orderNumber = generateOrderNumber();
    $stmt = $db->prepare("INSERT INTO orders (order_number, user_id, service_type, patch_type, status, admin_notes) VALUES (?, ?, ?, ?, 'submitted', ?)");
    $stmt->execute([$orderNumber, $userId, $serviceType, $patchType, $instructions]);
    $orderId = $db->lastInsertId();
    
    $detailFields = [
        'patch_size_width', 'patch_size_height', 'size_unit', 'patch_shape', 
        'quantity', 'backing_type', 'border_type', 'shipping_address'
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

$pageTitle = 'Custom Patches Order';
$currentPage = 'new-order.php';
include 'includes/sidebar.php';
?>

<div class="customer-content">
    <div class="customer-header">
        <h1>Custom Patches</h1>
        <p>Fill in the details for your custom patches order</p>
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
                        <label class="form-label">Patch Type</label>
                        <select name="patch_type" class="form-select">
                            <option value="digital">Digital Only</option>
                            <option value="physical">Physical</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Width</label>
                        <input type="number" name="patch_size_width" class="form-control" step="0.1" placeholder="10">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Height</label>
                        <input type="number" name="patch_size_height" class="form-control" step="0.1" placeholder="8">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Unit</label>
                        <select name="size_unit" class="form-select">
                            <option value="cm">cm</option>
                            <option value="in">inches</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Patch Shape</label>
                        <input type="text" name="patch_shape" class="form-control" placeholder="Circle, Rectangle, Custom">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" placeholder="50">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Backing Type</label>
                        <select name="backing_type" class="form-select">
                            <option value="Iron-on">Iron-on</option>
                            <option value="Sew-on">Sew-on</option>
                            <option value="Velcro">Velcro</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Border Type</label>
                        <select name="border_type" class="form-select">
                            <option value="Merrowed">Merrowed</option>
                            <option value="Heat-seal">Heat-seal</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                    <div class="col-12" id="shipping_address_field" style="display:none;">
                        <label class="form-label">Shipping Address</label>
                        <textarea name="shipping_address" class="form-control" rows="3" placeholder="Full shipping address"></textarea>
                    </div>
                    <script>
                        document.querySelector('select[name="patch_type"]')?.addEventListener('change', function() {
                            document.getElementById('shipping_address_field').style.display = this.value === 'physical' ? 'block' : 'none';
                        });
                    </script>

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