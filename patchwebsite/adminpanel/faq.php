<?php
// /patchwebsite/adminpanel/faq.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

$db = getDB();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM faq WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: faq.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $question = sanitize($_POST['question']);
    $answer = sanitize($_POST['answer']);
    $category = sanitize($_POST['category']);
    $sort_order = (int)$_POST['sort_order'];
    $status = sanitize($_POST['status']);

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE faq SET question = ?, answer = ?, category = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$question, $answer, $category, $sort_order, $status, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO faq (question, answer, category, sort_order, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$question, $answer, $category, $sort_order, $status]);
    }
    header('Location: faq.php');
    exit;
}

$faqs = $db->query("SELECT * FROM faq ORDER BY sort_order")->fetchAll();

$pageTitle = 'FAQ';
$currentPage = 'faq.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>FAQ</h1>
            <p>Manage frequently asked questions</p>
        </div>
        <button type="button" class="btn btn-primary-cyan" data-bs-toggle="modal" data-bs-target="#faqModal">
            <i class="fas fa-plus"></i> Add New FAQ
        </button>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Sort</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($faqs) > 0): ?>
                        <?php foreach ($faqs as $faq): ?>
                        <tr>
                            <td><?php echo $faq['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($faq['question']); ?></strong></td>
                            <td><?php echo nl2br(htmlspecialchars(substr($faq['answer'], 0, 80))); ?><?php echo strlen($faq['answer']) > 80 ? '...' : ''; ?></td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($faq['category'] ?? 'General'); ?></span></td>
                            <td><span class="badge <?php echo $faq['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $faq['status']; ?></span></td>
                            <td><?php echo $faq['sort_order']; ?></td>
                            <td>
                                <a href="faq.php?edit=<?php echo $faq['id']; ?>" class="btn btn-sm btn-outline-cyan" data-bs-toggle="modal" data-bs-target="#faqModal" onclick="editFaq(<?php echo htmlspecialchars(json_encode($faq)); ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="faq.php?delete=<?php echo $faq['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this FAQ?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">No FAQs found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="faqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--bg-card); color: var(--white);">
            <div class="modal-header" style="border-color: var(--border-color);">
                <h5 class="modal-title"><i class="fas fa-question-circle text-cyan"></i> Add FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id" value="0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Question *</label>
                            <input type="text" name="question" id="edit_question" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Answer *</label>
                            <textarea name="answer" id="edit_answer" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" id="edit_category" class="form-control" placeholder="e.g., General, Pricing, Technical">
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
function editFaq(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_question').value = item.question;
    document.getElementById('edit_answer').value = item.answer;
    document.getElementById('edit_category').value = item.category || '';
    document.getElementById('edit_sort').value = item.sort_order || 0;
    document.getElementById('edit_status').value = item.status || 'active';
    document.querySelector('#faqModal .modal-title').innerHTML = '<i class="fas fa-edit text-cyan"></i> Edit FAQ';
}
</script>

<?php include 'includes/footer.php'; ?>