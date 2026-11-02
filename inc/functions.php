<?php
// inc/functions.php - Helper Functions

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function validateVIN($vin) {
    $vin = strtoupper(preg_replace('/[^A-Z0-9]/', '', $vin));
    return (strlen($vin) == 17);
}

function generateOrderID() {
    return 'VIN-' . strtoupper(uniqid());
}

function getPackageLabel($package) {
    $labels = [
        'basic' => 'Basic ($30)',
        'premium' => 'Premium ($78)',
        'ultimate' => 'Ultimate ($50)'
    ];
    return $labels[$package] ?? ucfirst($package);
}
?>