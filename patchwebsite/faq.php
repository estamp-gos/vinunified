<?php
// /patchwebsite/faq.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = getDB();

// Get category filter
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';

// Build query
$sql = "SELECT * FROM faq WHERE status = 'active'";
$params = [];

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY sort_order";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter
$stmt = $db->query("SELECT DISTINCT category FROM faq WHERE status = 'active' AND category IS NOT NULL");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'FAQ - ' . SITE_NAME;
$activePage = 'faq';

include 'includes/header.php';
?>

<!-- FAQ Section -->
<section class="faq-section" style="padding-top: 120px;">
    <div class="container">
        <div class="section-title">
            <h2>Frequently Asked <span>Questions</span></h2>
            <p>Everything you need to know about our services</p>
            <div class="line"></div>
        </div>

        <!-- Category Filter -->
        <?php if (!empty($categories)): ?>
        <div class="portfolio-filters mb-4">
            <a href="<?php echo SITE_URL; ?>faq" class="btn <?php echo empty($category) ? 'active' : ''; ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo SITE_URL; ?>faq?category=<?php echo urlencode($cat); ?>" class="btn <?php echo $category === $cat ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if (count($faqs) > 0): ?>
                <div class="accordion" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" 
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#faq<?php echo $faq['id']; ?>"
                                    aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                <i class="fas fa-question-circle text-cyan me-2"></i>
                                <?php echo htmlspecialchars($faq['question']); ?>
                            </button>
                        </h2>
                        <div id="faq<?php echo $faq['id']; ?>" 
                             class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                <?php if (!empty($faq['category'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Category: <?php echo htmlspecialchars($faq['category']); ?></small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center">
                    <p class="text-muted">No FAQs found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Still have questions? -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-8 text-center">
                <div class="card-punchy" style="border-color: var(--cyan);">
                    <h4><i class="fas fa-comment-dots text-cyan"></i> Still have questions?</h4>
                    <p class="text-muted">Can't find the answer you're looking for? Please chat with our friendly team.</p>
                    <a href="<?php echo SITE_URL; ?>contact" class="btn-hero-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>