<?php
// /patchwebsite/blog-detail.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    redirect(SITE_URL . 'blog');
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM blog WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    redirect(SITE_URL . 'blog');
}

// Get related posts (same category)
$stmt = $db->prepare("SELECT * FROM blog WHERE status = 'published' AND category = ? AND slug != ? ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$post['category'], $post['slug']]);
$relatedPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update view count (optional - add views column if needed)
// $stmt = $db->prepare("UPDATE blog SET views = views + 1 WHERE id = ?");
// $stmt->execute([$post['id']]);

$pageTitle = htmlspecialchars($post['seo_title'] ?? $post['title']) . ' - ' . SITE_NAME;
$activePage = 'blog';

include 'includes/header.php';
?>

<!-- Blog Detail Section - Full Width -->
<section class="blog-detail-section" style="padding-top: 120px; padding-bottom: 60px;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="blog-detail-card">
                    <!-- Featured Image -->
                    <img src="<?php echo htmlspecialchars($post['featured_image'] ?? 'https://img.stitchnhide.com//upload/6a9ab8ba73122.png'); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                         class="img-fluid w-100 rounded mb-4" 
                         style="max-height: 500px; object-fit: cover;">

                    <!-- Meta Info -->
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <span class="badge-format" style="background: rgba(0, 240, 255, 0.15); color: var(--cyan); border: none; padding: 6px 16px;">
                            <?php echo htmlspecialchars($post['category'] ?? 'General'); ?>
                        </span>
                        <span class="text-muted small">
                            <i class="far fa-calendar-alt text-cyan"></i> <?php echo formatDate($post['created_at']); ?>
                        </span>
                        <span class="text-muted small">
                            <i class="far fa-clock text-cyan"></i> <?php echo timeAgo($post['created_at']); ?>
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="mt-2 display-4 fw-bold"><?php echo htmlspecialchars($post['title']); ?></h1>

                    <!-- Author info (if you have authors table, can expand) -->
                    <div class="text-muted small mb-4">
                        <i class="far fa-user text-cyan"></i> PunchyArts Team
                    </div>

                    <!-- Divider -->
                    <hr class="border-color" style="border-color: var(--border-color);">

                    <!-- Content -->
                    <div class="blog-content fs-5" style="color: #d0d5e0; line-height: 1.9;">
                        <?php echo $post['content']; ?>
                    </div>

                    <!-- Divider -->
                    <hr class="border-color my-4" style="border-color: var(--border-color);">

                    <!-- Share & Actions -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <span class="text-muted me-2">Share this post:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . 'blog-detail?slug=' . $post['slug']); ?>" 
                               target="_blank" class="text-cyan me-2" style="font-size: 20px;">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . 'blog-detail?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                               target="_blank" class="text-cyan me-2" style="font-size: 20px;">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(SITE_URL . 'blog-detail?slug=' . $post['slug']); ?>" 
                               target="_blank" class="text-cyan me-2" style="font-size: 20px;">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="mailto:?subject=<?php echo urlencode($post['title']); ?>&body=<?php echo urlencode(SITE_URL . 'blog-detail?slug=' . $post['slug']); ?>" 
                               class="text-cyan" style="font-size: 20px;">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                        <a href="<?php echo SITE_URL; ?>blog" class="btn-hero-primary mt-2 mt-md-0">
                            <i class="fas fa-arrow-left"></i> Back to Blog
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Posts -->
        <?php if (count($relatedPosts) > 0): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="text-cyan mb-4">Related <span style="color: var(--white);">Posts</span></h3>
            </div>
            <?php foreach ($relatedPosts as $related): ?>
            <div class="col-lg-4 col-md-6">
                <div class="blog-card">
                    <img src="<?php echo htmlspecialchars($related['featured_image'] ?? 'https://img.stitchnhide.com//upload/6a9ab8ba73122.png'); ?>" 
                         alt="<?php echo htmlspecialchars($related['title']); ?>">
                    <div class="body">
                        <span class="category"><?php echo htmlspecialchars($related['category'] ?? 'General'); ?></span>
                        <h5><a href="<?php echo SITE_URL; ?>blog-detail?slug=<?php echo $related['slug']; ?>"><?php echo htmlspecialchars($related['title']); ?></a></h5>
                        <div class="date"><i class="far fa-calendar-alt"></i> <?php echo formatDate($related['created_at']); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>