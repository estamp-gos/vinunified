<?php
// /patchwebsite/adminpanel/portfolio.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

$db = getDB();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get image path to delete file
    $stmt = $db->prepare("SELECT image FROM portfolio WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if ($item && !empty($item['image']) && strpos($item['image'], 'uploads/') === 0) {
        $filePath = __DIR__ . '/../' . $item['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    $stmt = $db->prepare("DELETE FROM portfolio WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: portfolio.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $sort_order = (int)$_POST['sort_order'];
    $status = sanitize($_POST['status']);
    
    $image = '';
    $before_image = '';
    $after_image = '';
    
    // Handle main image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = UPLOAD_PATH_PORTFOLIO;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $newName = uniqid() . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $newName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = 'uploads/portfolio/' . $newName;
        }
    }
    
    // Handle before image upload
    if (isset($_FILES['before_image']) && $_FILES['before_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = UPLOAD_PATH_PORTFOLIO;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES['before_image']['name'], PATHINFO_EXTENSION));
        $newName = 'before_' . uniqid() . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $newName;
        if (move_uploaded_file($_FILES['before_image']['tmp_name'], $targetPath)) {
            $before_image = 'uploads/portfolio/' . $newName;
        }
    }
    
    // Handle after image upload
    if (isset($_FILES['after_image']) && $_FILES['after_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = UPLOAD_PATH_PORTFOLIO;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES['after_image']['name'], PATHINFO_EXTENSION));
        $newName = 'after_' . uniqid() . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $newName;
        if (move_uploaded_file($_FILES['after_image']['tmp_name'], $targetPath)) {
            $after_image = 'uploads/portfolio/' . $newName;
        }
    }

    if ($id > 0) {
        // Update - keep existing images if no new ones uploaded
        if (empty($image)) {
            $stmt = $db->prepare("SELECT image FROM portfolio WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            $image = $existing['image'] ?? '';
        }
        if (empty($before_image)) {
            $stmt = $db->prepare("SELECT before_image FROM portfolio WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            $before_image = $existing['before_image'] ?? '';
        }
        if (empty($after_image)) {
            $stmt = $db->prepare("SELECT after_image FROM portfolio WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            $after_image = $existing['after_image'] ?? '';
        }
        
        $stmt = $db->prepare("UPDATE portfolio SET title = ?, description = ?, category = ?, image = ?, before_image = ?, after_image = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $description, $category, $image, $before_image, $after_image, $sort_order, $status, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO portfolio (title, description, category, image, before_image, after_image, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $category, $image, $before_image, $after_image, $sort_order, $status]);
    }
    header('Location: portfolio.php');
    exit;
}

$portfolioItems = $db->query("SELECT * FROM portfolio ORDER BY sort_order")->fetchAll();
$categories = ['digitizing', 'vector', 'character', 'patches'];

$pageTitle = 'Portfolio';
$currentPage = 'portfolio.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Portfolio</h1>
            <p>Manage portfolio items</p>
        </div>
        <button type="button" class="btn btn-primary-cyan" data-bs-toggle="modal" data-bs-target="#portfolioModal">
            <i class="fas fa-plus"></i> Add New
        </button>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Sort</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($portfolioItems) > 0): ?>
                        <?php foreach ($portfolioItems as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['image'] ? SITE_URL . $item['image'] : 'https://img.stitchnhide.com//upload/6a9ab8ba73122.png'); ?>" 
                                     alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo ucfirst($item['category']); ?></td>
                            <td><span class="badge <?php echo $item['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $item['status']; ?></span></td>
                            <td><?php echo $item['sort_order']; ?></td>
                            <td>
                                <a href="portfolio.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-cyan" data-bs-toggle="modal" data-bs-target="#portfolioModal" onclick="editPortfolio(<?php echo htmlspecialchars(json_encode($item)); ?>)">Edit</a>
                                <a href="portfolio.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No portfolio items.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="portfolioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--bg-card); color: var(--white);">
            <div class="modal-header" style="border-color: var(--border-color);">
                <h5 class="modal-title"><i class="fas fa-images text-cyan"></i> Add Portfolio Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id" value="0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category" id="edit_category" class="form-select" required>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>"><?php echo ucfirst($cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Main Image *</label>
                            <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
                            <small class="text-muted">JPG, PNG, WebP</small>
                            <div id="current_image" style="display:none; margin-top:5px;">
                                <img src="" alt="Current" style="max-width:100px; max-height:60px;">
                            </div>
                        </div>
                        <!--<div class="col-md-4">-->
                        <!--    <label class="form-label">Before Image</label>-->
                        <!--    <input type="file" name="before_image" id="edit_before" class="form-control" accept="image/*">-->
                        <!--    <small class="text-muted">For slider comparison</small>-->
                        <!--    <div id="current_before" style="display:none; margin-top:5px;">-->
                        <!--        <img src="" alt="Current Before" style="max-width:100px; max-height:60px;">-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-4">-->
                        <!--    <label class="form-label">After Image</label>-->
                        <!--    <input type="file" name="after_image" id="edit_after" class="form-control" accept="image/*">-->
                        <!--    <small class="text-muted">For slider comparison</small>-->
                        <!--    <div id="current_after" style="display:none; margin-top:5px;">-->
                        <!--        <img src="" alt="Current After" style="max-width:100px; max-height:60px;">-->
                        <!--    </div>-->
                        <!--</div>-->
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
function editPortfolio(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_title').value = item.title;
    document.getElementById('edit_description').value = item.description || '';
    document.getElementById('edit_category').value = item.category;
    document.getElementById('edit_sort').value = item.sort_order || 0;
    document.getElementById('edit_status').value = item.status || 'active';
    
    // Show current images
    if (item.image) {
        document.getElementById('current_image').style.display = 'block';
        document.getElementById('current_image').querySelector('img').src = '<?php echo SITE_URL; ?>' + item.image;
    } else {
        document.getElementById('current_image').style.display = 'none';
    }
    if (item.before_image) {
        document.getElementById('current_before').style.display = 'block';
        document.getElementById('current_before').querySelector('img').src = '<?php echo SITE_URL; ?>' + item.before_image;
    } else {
        document.getElementById('current_before').style.display = 'none';
    }
    if (item.after_image) {
        document.getElementById('current_after').style.display = 'block';
        document.getElementById('current_after').querySelector('img').src = '<?php echo SITE_URL; ?>' + item.after_image;
    } else {
        document.getElementById('current_after').style.display = 'none';
    }
    
    document.querySelector('#portfolioModal .modal-title').innerHTML = '<i class="fas fa-edit text-cyan"></i> Edit Portfolio Item';
}
</script>

<?php include 'includes/footer.php'; ?>