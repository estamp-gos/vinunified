<?php
// /patchwebsite/adminpanel/settings.php
require_once 'includes/config.php';
require_once 'includes/header.php';

$db = getDB();

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name', 'site_tagline', 'site_email', 'site_phone', 'site_address',
        'social_facebook', 'social_instagram', 'social_youtube', 'social_linkedin', 'social_twitter',
        'meta_description', 'meta_keywords'
    ];

    foreach ($settings as $key) {
        $value = sanitize($_POST[$key] ?? '');
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }

    $message = 'Settings updated successfully!';
    header('Location: settings.php?updated=1');
    exit;
}

$settings = [];
$stmt = $db->query("SELECT setting_key, setting_value FROM settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$pageTitle = 'Settings';
$currentPage = 'settings.php';
include 'includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <h1>Settings</h1>
        <p>Manage website settings</p>
    </div>

    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Settings updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="admin-card-body">
            <form method="POST">
                <div class="row g-4">
                    <!-- Site Info -->
                    <div class="col-12">
                        <h5 class="text-cyan"><i class="fas fa-info-circle"></i> Site Information</h5>
                        <hr style="border-color: var(--border-color);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'PunchyArts'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Site Tagline</label>
                        <input type="text" name="site_tagline" class="form-control" value="<?php echo htmlspecialchars($settings['site_tagline'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="site_email" class="form-control" value="<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="site_phone" class="form-control" value="<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="site_address" class="form-control" value="<?php echo htmlspecialchars($settings['site_address'] ?? ''); ?>">
                    </div>

                    <!-- Social Media -->
                    <div class="col-12 mt-4">
                        <h5 class="text-cyan"><i class="fas fa-share-alt"></i> Social Media</h5>
                        <hr style="border-color: var(--border-color);">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fab fa-facebook text-cyan"></i> Facebook</label>
                        <input type="url" name="social_facebook" class="form-control" value="<?php echo htmlspecialchars($settings['social_facebook'] ?? ''); ?>" placeholder="https://facebook.com/...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fab fa-instagram text-cyan"></i> Instagram</label>
                        <input type="url" name="social_instagram" class="form-control" value="<?php echo htmlspecialchars($settings['social_instagram'] ?? ''); ?>" placeholder="https://instagram.com/...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fab fa-youtube text-cyan"></i> YouTube</label>
                        <input type="url" name="social_youtube" class="form-control" value="<?php echo htmlspecialchars($settings['social_youtube'] ?? ''); ?>" placeholder="https://youtube.com/...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fab fa-linkedin text-cyan"></i> LinkedIn</label>
                        <input type="url" name="social_linkedin" class="form-control" value="<?php echo htmlspecialchars($settings['social_linkedin'] ?? ''); ?>" placeholder="https://linkedin.com/...">
                    </div>

                    <!-- SEO -->
                    <div class="col-12 mt-4">
                        <h5 class="text-cyan"><i class="fas fa-search"></i> SEO</h5>
                        <hr style="border-color: var(--border-color);">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2"><?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($settings['meta_keywords'] ?? ''); ?>" placeholder="keyword1, keyword2, keyword3">
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary-cyan"><i class="fas fa-save"></i> Save Settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>