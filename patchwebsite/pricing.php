<?php
// /patchwebsite/pricing.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = getDB();
$stmt = $db->prepare("SELECT * FROM pricing WHERE status = 'active' ORDER BY sort_order");
$stmt->execute();
$pricingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Pricing - ' . SITE_NAME;
$activePage = 'pricing';

include 'includes/header.php';
?>

<!-- Pricing Section -->
<section class="pricing-section" style="padding-top: 120px;">
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

<?php include 'includes/footer.php'; ?>