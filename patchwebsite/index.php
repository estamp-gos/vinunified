<?php
// /patchwebsite/index.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = getDB();

// Get portfolio items for slider
$stmt = $db->prepare("SELECT * FROM portfolio WHERE status = 'active' ORDER BY sort_order LIMIT 6");
$stmt->execute();
$portfolioItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all portfolio items for grid
$stmt = $db->prepare("SELECT * FROM portfolio WHERE status = 'active' ORDER BY sort_order LIMIT 8");
$stmt->execute();
$allPortfolioItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get services
$stmt = $db->prepare("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get blog posts
$stmt = $db->prepare("SELECT * FROM blog WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get FAQs
$stmt = $db->prepare("SELECT * FROM faq WHERE status = 'active' ORDER BY sort_order LIMIT 6");
$stmt->execute();
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get pricing
$stmt = $db->prepare("SELECT * FROM pricing WHERE status = 'active' ORDER BY sort_order");
$stmt->execute();
$pricingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Home - ' . SITE_NAME;
$activePage = 'home';
$includeSlider = true;

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge">⚡ Welcome to PunchyArts</div>
                <h1>Premium Design Studio for <br><span>Embroidery & Vector</span></h1>
                <p class="hero-text">Professional embroidery digitizing, vector tracing, character art, and custom patches. Trusted by brands worldwide for high-quality design services.</p>
                <div class="btn-group">
                    <a href="<?php echo SITE_URL; ?>register" class="btn-hero-primary">Get Started</a>
                    <a href="<?php echo SITE_URL; ?>portfolio" class="btn-hero-secondary">View Portfolio</a>
                </div>
            </div>
            <div class="col-lg-5">
                <img src="<?php echo SITE_URL; ?>assets/images/hero-illustration.png" alt="<?php echo SITE_NAME; ?>" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <div class="section-title">
            <h2>Our <span>Services</span></h2>
            <p>Professional design services tailored to your needs</p>
            <div class="line"></div>
        </div>
        <div class="row g-4">
            <?php foreach ($services as $service): ?>
            <div class="col-lg-3 col-md-6">
                <div class="service-card">
                    <div class="service-image">
                        <img src="<?php echo SITE_URL; ?>assets/images/service-<?php echo $service['slug']; ?>.jpg" alt="<?php echo htmlspecialchars($service['name']); ?>">
                    </div>
                    <h5><?php echo htmlspecialchars($service['name']); ?></h5>
                    <p><?php echo htmlspecialchars(substr($service['description'], 0, 80)) . '...'; ?></p>
                    <a href="<?php echo SITE_URL; ?>services#<?php echo $service['slug']; ?>" class="btn-service">Learn More →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- Before/After Slider Section - Full Width (Static Images) -->
<section class="slider-section">
    <div class="container-fluid px-0">
        <div class="section-title">
            <h2>Before & <span>After</span></h2>
            <p>See the transformation from original to final</p>
            <div class="line"></div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="slider-container" id="beforeAfterSlider">
                    <!-- Static Before Image -->
                    <img src="https://img.stitchnhide.com//upload/6a9ab8ba73122.png" alt="Before">
                    <div class="after-image" style="clip-path: inset(0 50% 0 0);">
                        <!-- Static After Image -->
                        <img src="https://img.stitchnhide.com//upload/6a9ab8ba73122.png" alt="After">
                    </div>
                    <div class="slider-handle" id="sliderHandle">
                        <span class="slider-label before">Before</span>
                        <span class="slider-label after">After</span>
                    </div>
                </div>
                <p class="text-center text-muted mt-3">👆 Drag the slider to see before & after</p>
            </div>
        </div>
    </div>
</section>
<!-- Portfolio Grid Section -->
<section class="portfolio-grid-section">
    <div class="container">
        <div class="section-title">
            <h2>Our <span>Portfolio</span></h2>
            <p>Browse our latest work</p>
            <div class="line"></div>
        </div>

        <div class="row g-4">
            <?php if (count($allPortfolioItems) > 0): ?>
                <?php foreach ($allPortfolioItems as $item): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="portfolio-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="overlay">
                            <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                            <small><?php echo ucfirst($item['category']); ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No portfolio items found.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>portfolio" class="btn-hero-primary">View All Portfolio</a>
        </div>
    </div>
</section>

<!-- Pricing Section with Tabs -->
<section class="pricing-section">
    <div class="container">
        <div class="section-title">
            <h2>Simple <span>Pricing</span></h2>
            <p>Transparent pricing for all our services</p>
            <div class="line"></div>
        </div>

        <?php
        $groups = [];
        foreach ($pricingItems as $item) {
            $groups[$item['service_type']][] = $item;
        }
        $serviceTypes = array_keys($groups);
        ?>

        <!-- Tabs -->
        <ul class="nav nav-pills pricing-tabs justify-content-center" id="pricingTabs" role="tablist">
            <?php foreach ($serviceTypes as $index => $type): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" 
                        id="tab-<?php echo $type; ?>" 
                        data-bs-toggle="pill" 
                        data-bs-target="#pricing-<?php echo $type; ?>" 
                        type="button" role="tab">
                    <?php echo getServiceName($type); ?>
                </button>
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-4">
            <?php foreach ($groups as $type => $items): ?>
            <div class="tab-pane fade <?php echo $type === $serviceTypes[0] ? 'show active' : ''; ?>" 
                 id="pricing-<?php echo $type; ?>" role="tabpanel">
                <div class="row g-4">
                    <?php foreach ($items as $index => $item): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="pricing-card <?php echo $index === 1 ? 'featured' : ''; ?>">
                            <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                            <p class="text-muted small"><?php echo htmlspecialchars($item['description']); ?></p>
                            <div class="price"><?php echo htmlspecialchars($item['price']); ?></div>
                            <ul>
                                <?php 
                                $features = explode(',', $item['features'] ?? '');
                                foreach ($features as $feature): 
                                ?>
                                <li><i class="fas fa-check"></i> <?php echo htmlspecialchars(trim($feature)); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="<?php echo SITE_URL; ?>register" class="btn <?php echo $index === 1 ? 'btn-primary-cyan' : 'btn-outline-cyan'; ?> w-100">Get Started</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="section-title">
            <h2>Frequently Asked <span>Questions</span></h2>
            <p>Everything you need to know about our services</p>
            <div class="line"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo $faq['id']; ?>">
                                <?php echo htmlspecialchars($faq['question']); ?>
                            </button>
                        </h2>
                        <div id="faq<?php echo $faq['id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="blog-section">
    <div class="container">
        <div class="section-title">
            <h2>Latest <span>Blog</span></h2>
            <p>Insights and tips from our design experts</p>
            <div class="line"></div>
        </div>
        <div class="row g-4">
            <?php foreach ($blogPosts as $post): ?>
            <div class="col-md-4">
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
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>