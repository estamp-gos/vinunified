<?php
// /patchwebsite/adminpanel/services.php
require_once 'includes/config.php';
require_once 'includes/header.php';

$db = getDB();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: services.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = sanitize($_POST['name']);
    $slug = sanitize($_POST['slug']);
    $description = sanitize($_POST['description']);
    $icon = sanitize($_POST['icon']);
    $sort_order = (int)$_POST['sort_order'];
    $status = sanitize($_POST['status']);

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE services SET name = ?, slug = ?, description = ?, icon = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $description, $icon, $sort_order, $status, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO services (name, slug, description, icon, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $description, $icon, $sort_order, $status]);
    }
    header('Location: services.php');
    exit;
}

$services = $db->query("SELECT * FROM services ORDER BY sort_order")->fetchAll();

$pageTitle = 'Services';
$currentPage = 'services.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Services</h1>
            <p>Manage services</p>
        </div>
        <button type="button" class="btn btn-primary-cyan" data-bs-toggle="modal" data-bs-target="#serviceModal">
            <i class="fas fa-plus"></i> Add New Service
        </button>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Icon</th>
                            <th>Status</th>
                            <th>Sort</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($services) > 0): ?>
                        <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo $service['id']; ?></td>
                            <td><?php echo htmlspecialchars($service['name']); ?></td>
                            <td><?php echo htmlspecialchars($service['slug']); ?></td>
                            <td><i class="fas <?php echo htmlspecialchars($service['icon']); ?>"></i></td>
                            <td><span class="badge <?php echo $service['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $service['status']; ?></span></td>
                            <td><?php echo $service['sort_order']; ?></td>
                            <td>
                                <a href="services.php?edit=<?php echo $service['id']; ?>" class="btn btn-sm btn-outline-cyan" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)">Edit</a>
                                <a href="services.php?delete=<?php echo $service['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this service?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No services found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--bg-card); color: var(--white);">
            <div class="modal-header" style="border-color: var(--border-color);">
                <h5 class="modal-title"><i class="fas fa-cogs text-cyan"></i> Add Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id" value="0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="e.g., Embroidery Digitizing" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Slug *</label>
                            <input type="text" name="slug" id="edit_slug" class="form-control" placeholder="embroidery-digitizing" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Icon (FontAwesome class)</label>
                            <input type="text" name="icon" id="edit_icon" class="form-control" placeholder="fa-vector-pen">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" id="edit_sort" class="form-control" value="0">
                        </div>
                        <div class="col-md-3">
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
function editService(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_name').value = item.name;
    document.getElementById('edit_slug').value = item.slug;
    document.getElementById('edit_description').value = item.description || '';
    document.getElementById('edit_icon').value = item.icon || '';
    document.getElementById('edit_sort').value = item.sort_order || 0;
    document.getElementById('edit_status').value = item.status || 'active';
    document.querySelector('#serviceModal .modal-title').innerHTML = '<i class="fas fa-edit text-cyan"></i> Edit Service';
}
</script>

<?php include 'includes/footer.php'; ?>