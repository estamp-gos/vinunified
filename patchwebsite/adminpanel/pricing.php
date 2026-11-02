<?php
// /patchwebsite/adminpanel/pricing.php
require_once 'includes/config.php';
require_once 'includes/header.php';

$db = getDB();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM pricing WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: pricing.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $service_type = sanitize($_POST['service_type']);
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = sanitize($_POST['price']);
    $features = sanitize($_POST['features']);
    $sort_order = (int)$_POST['sort_order'];
    $status = sanitize($_POST['status']);

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE pricing SET service_type = ?, title = ?, description = ?, price = ?, features = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$service_type, $title, $description, $price, $features, $sort_order, $status, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO pricing (service_type, title, description, price, features, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$service_type, $title, $description, $price, $features, $sort_order, $status]);
    }
    header('Location: pricing.php');
    exit;
}

$pricingItems = $db->query("SELECT * FROM pricing ORDER BY service_type, sort_order")->fetchAll();
$serviceTypes = ['digitizing', 'vector', 'character', 'patches'];

$pageTitle = 'Pricing';
$currentPage = 'pricing.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Pricing</h1>
            <p>Manage pricing plans</p>
        </div>
        <button type="button" class="btn btn-primary-cyan" data-bs-toggle="modal" data-bs-target="#pricingModal">
            <i class="fas fa-plus"></i> Add New Plan
        </button>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Service</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Sort</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pricingItems) > 0): ?>
                        <?php foreach ($pricingItems as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo getServiceName($item['service_type']); ?></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo htmlspecialchars($item['price']); ?></td>
                            <td><span class="badge <?php echo $item['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $item['status']; ?></span></td>
                            <td><?php echo $item['sort_order']; ?></td>
                            <td>
                                <a href="pricing.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-cyan" data-bs-toggle="modal" data-bs-target="#pricingModal" onclick="editPricing(<?php echo htmlspecialchars(json_encode($item)); ?>)">Edit</a>
                                <a href="pricing.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this plan?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No pricing plans found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="pricingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--bg-card); color: var(--white);">
            <div class="modal-header" style="border-color: var(--border-color);">
                <h5 class="modal-title"><i class="fas fa-tag text-cyan"></i> Add Pricing Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id" value="0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Service Type *</label>
                            <select name="service_type" id="edit_service" class="form-select" required>
                                <?php foreach ($serviceTypes as $type): ?>
                                <option value="<?php echo $type; ?>"><?php echo getServiceName($type); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" id="edit_title" class="form-control" placeholder="e.g., Basic Digitizing" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" id="edit_description" class="form-control" placeholder="Brief description">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price *</label>
                            <input type="text" name="price" id="edit_price" class="form-control" placeholder="From $15" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Features (comma separated)</label>
                            <input type="text" name="features" id="edit_features" class="form-control" placeholder="Feature 1, Feature 2, Feature 3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" id="edit_sort" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-color: var(--border-color);">
                    <button type="button" class="btn btn-outline-cyan" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-cyan">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPricing(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_service').value = item.service_type;
    document.getElementById('edit_title').value = item.title;
    document.getElementById('edit_description').value = item.description || '';
    document.getElementById('edit_price').value = item.price;
    document.getElementById('edit_features').value = item.features || '';
    document.getElementById('edit_sort').value = item.sort_order || 0;
    document.getElementById('edit_status').value = item.status || 'active';
    document.querySelector('#pricingModal .modal-title').innerHTML = '<i class="fas fa-edit text-cyan"></i> Edit Pricing Plan';
}
</script>

<?php include 'includes/footer.php'; ?>