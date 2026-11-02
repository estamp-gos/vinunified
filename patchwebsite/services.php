<?php
// /patchwebsite/services.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = getDB();
$stmt = $db->prepare("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Services - ' . SITE_NAME;
$activePage = 'services';

include 'includes/header.php';
?>

<!-- Services Page -->
<section class="services-page" style="padding-top: 120px; padding-bottom: 60px;">
    <div class="container">
        <div class="section-title">
            <h2>Our <span>Services</span></h2>
            <p>Professional design services tailored to your needs</p>
            <div class="line"></div>
        </div>

        <?php foreach ($services as $index => $service): ?>
        <div class="service-detail" id="<?php echo $service['slug']; ?>">
            <div class="row align-items-center g-5 <?php echo $index % 2 === 0 ? '' : 'flex-row-reverse'; ?>">
                <div class="col-lg-6">
                    <img src="<?php echo SITE_URL; ?>assets/images/service-<?php echo $service['slug']; ?>.jpg" 
                         alt="<?php echo htmlspecialchars($service['name']); ?>" 
                         class="img-fluid rounded service-detail-img">
                </div>
                <div class="col-lg-6">
                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                    <p class="text-muted"><?php echo htmlspecialchars($service['description']); ?></p>
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo CUSTOMER_URL; ?>new-order?service=<?php echo $service['slug']; ?>" class="btn-hero-primary">Place Order</a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>login" class="btn-hero-primary">Login to Order</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>