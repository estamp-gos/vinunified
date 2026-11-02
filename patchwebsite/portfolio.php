<?php
// /patchwebsite/portfolio.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = getDB();
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';

if ($category !== 'all') {
    $stmt = $db->prepare("SELECT * FROM portfolio WHERE status = 'active' AND category = ? ORDER BY sort_order");
    $stmt->execute([$category]);
} else {
    $stmt = $db->prepare("SELECT * FROM portfolio WHERE status = 'active' ORDER BY sort_order");
    $stmt->execute();
}
$portfolioItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = ['All' => 'all', 'Digitizing' => 'digitizing', 'Vector' => 'vector', 'Character Art' => 'character', 'Patches' => 'patches'];

$pageTitle = 'Portfolio - ' . SITE_NAME;
$activePage = 'portfolio';

include 'includes/header.php';
?>

<!-- Portfolio Section -->
<section class="portfolio-section" style="padding-top: 120px;">
    <div class="container">
        <div class="section-title">
            <h2>Our <span>Portfolio</span></h2>
            <p>Browse our latest work</p>
            <div class="line"></div>
        </div>

        <!-- Filters -->
        <div class="portfolio-filters">
            <?php foreach ($categories as $label => $value): ?>
            <a href="<?php echo SITE_URL; ?>portfolio?category=<?php echo $value; ?>" class="btn <?php echo ($category === $value) ? 'active' : ''; ?>">
                <?php echo $label; ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="row g-4">
            <?php if (count($portfolioItems) > 0): ?>
                <?php foreach ($portfolioItems as $item): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="portfolio-item">
                        <img src="<?php echo htmlspecialchars($item['image'] ? SITE_URL . $item['image'] : 'https://img.stitchnhide.com//upload/6a9ab8ba73122.png'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="overlay">
                            <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                            <small><?php echo ucfirst($item['category']); ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No portfolio items found in this category.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>