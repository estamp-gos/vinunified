<?php
// /patchwebsite/adminpanel/blog.php

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
    $stmt = $db->prepare("SELECT featured_image FROM blog WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if ($post && !empty($post['featured_image']) && strpos($post['featured_image'], 'uploads/') === 0) {
        $filePath = __DIR__ . '/../' . $post['featured_image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    $stmt = $db->prepare("DELETE FROM blog WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: blog.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = sanitize($_POST['title']);
    $slug = sanitize($_POST['slug']);
    $content = $_POST['content'];
    $category = sanitize($_POST['category']);
    $seo_title = sanitize($_POST['seo_title']);
    $seo_description = sanitize($_POST['seo_description']);
    $status = sanitize($_POST['status']);
    
    $featured_image = '';
    
    // Handle image upload
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = UPLOAD_PATH_BLOG;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        $newName = uniqid() . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $newName;
        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
            $featured_image = 'uploads/blog/' . $newName;
        }
    }

    if ($id > 0) {
        // Update - keep existing image if no new one uploaded
        if (empty($featured_image)) {
            $stmt = $db->prepare("SELECT featured_image FROM blog WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            $featured_image = $existing['featured_image'] ?? '';
        }
        
        $stmt = $db->prepare("UPDATE blog SET title = ?, slug = ?, content = ?, category = ?, featured_image = ?, seo_title = ?, seo_description = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $slug, $content, $category, $featured_image, $seo_title, $seo_description, $status, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO blog (title, slug, content, category, featured_image, seo_title, seo_description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $content, $category, $featured_image, $seo_title, $seo_description, $status]);
    }
    header('Location: blog.php');
    exit;
}

$posts = $db->query("SELECT * FROM blog ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'Blog';
$currentPage = 'blog.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>Blog</h1>
            <p>Manage blog posts</p>
        </div>
        <button type="button" class="btn btn-primary-cyan" data-bs-toggle="modal" data-bs-target="#blogModal">
            <i class="fas fa-plus"></i> Add New Post
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
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($posts) > 0): ?>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo $post['id']; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($post['featured_image'] ? SITE_URL . $post['featured_image'] : 'https://img.stitchnhide.com//upload/6a9ab8ba73122.png'); ?>" 
                                     alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars($post['category'] ?? '-'); ?></td>
                            <td><span class="badge <?php echo $post['status'] === 'published' ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $post['status']; ?></span></td>
                            <td><?php echo formatDate($post['created_at']); ?></td>
                            <td>
                                <a href="blog.php?edit=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-cyan" data-bs-toggle="modal" data-bs-target="#blogModal" onclick="editBlog(<?php echo htmlspecialchars(json_encode($post)); ?>)">Edit</a>
                                <a href="blog.php?delete=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No blog posts.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="blogModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="background: var(--bg-card); color: var(--white);">
            <div class="modal-header" style="border-color: var(--border-color);">
                <h5 class="modal-title"><i class="fas fa-newspaper text-cyan"></i> Add Blog Post</h5>
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
                            <label class="form-label">Slug *</label>
                            <input type="text" name="slug" id="edit_slug" class="form-control" placeholder="url-friendly-name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" id="edit_category" class="form-control" placeholder="e.g., Digitizing, Vector, Patches">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Featured Image</label>
                            <input type="file" name="featured_image" id="edit_image" class="form-control" accept="image/*">
                            <small class="text-muted">JPG, PNG, WebP</small>
                            <div id="current_image" style="display:none; margin-top:5px;">
                                <img src="" alt="Current" style="max-width:100px; max-height:60px; border-radius:6px;">
                                <span class="text-muted small"> Current image</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Content *</label>
                            <textarea name="content" id="edit_content" class="form-control" rows="8" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SEO Title</label>
                            <input type="text" name="seo_title" id="edit_seo_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SEO Description</label>
                            <textarea name="seo_description" id="edit_seo_desc" class="form-control" rows="2"></textarea>
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
function editBlog(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_title').value = item.title;
    document.getElementById('edit_slug').value = item.slug;
    document.getElementById('edit_category').value = item.category || '';
    document.getElementById('edit_status').value = item.status;
    document.getElementById('edit_content').value = item.content;
    document.getElementById('edit_seo_title').value = item.seo_title || '';
    document.getElementById('edit_seo_desc').value = item.seo_description || '';
    
    // Show current image
    if (item.featured_image) {
        document.getElementById('current_image').style.display = 'block';
        document.getElementById('current_image').querySelector('img').src = '<?php echo SITE_URL; ?>' + item.featured_image;
    } else {
        document.getElementById('current_image').style.display = 'none';
    }
    
    document.querySelector('#blogModal .modal-title').innerHTML = '<i class="fas fa-edit text-cyan"></i> Edit Blog Post';
}
</script>

<?php include 'includes/footer.php'; ?>