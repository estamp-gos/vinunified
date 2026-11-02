<?php
// /patchwebsite/logout.php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Logout user
logoutUser();

// Set flash message
setFlash('success', 'You have been logged out successfully.');

// Redirect to home
redirect(SITE_URL);
exit;