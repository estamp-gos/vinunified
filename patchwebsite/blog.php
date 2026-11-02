<?php
// /patchwebsite/blog.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = getDB();

// Get category filter
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// Build query
$sql = "SELECT * FROM blog WHERE status = 'published'";
$params = [];

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter
$stmt = $db->query("SELECT DISTINCT category FROM blog WHERE status = 'published' AND category IS NOT NULL");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Blog - ' . SITE_NAME;
$activePage = 'blog';

include 'includes/header.php';
?>

<!-- Blog Section -->
<section class="blog-section" style="padding-top: 120px;">
    <div class="container">
        <div class="section-title">
            <h2>Latest <span>Blog</span></h2>
            <p>Insights and tips from our design experts</p>
            <div class="line"></div>
        </div>

        <!-- Category Filter -->
        <?php if (!empty($categories)): ?>
        <div class="portfolio-filters mb-4">
            <a href="<?php echo SITE_URL; ?>blog" class="btn <?php echo empty($category) ? 'active' : ''; ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo SITE_URL; ?>blog?category=<?php echo urlencode($cat); ?>" class="btn <?php echo $category === $cat ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="blog-card">
                        <img src="<?php echo htmlspecialchars($post['featured_image'] ?? 'https://img.stitchnhide.com//upload/6a9ab8ba73122.png'); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <div class="body">
                            <span class="category"><?php echo htmlspecialchars($post['category'] ?? 'General'); ?></span>
                            <h5><a href="<?php echo SITE_URL; ?>blog-detail?slug=<?php echo $post['slug']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h5>
                            <p><?php echo truncate(strip_tags($post['content']), 120); ?></p>
                            <div class="date"><i class="far fa-calendar-alt"></i> <?php echo formatDate($post['created_at']); ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No blog posts found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>