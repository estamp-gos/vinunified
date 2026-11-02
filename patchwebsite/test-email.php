<?php
// /patchwebsite/test-email.php

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/mail.php';

echo "<h1>📧 Email Test</h1>";

echo "<h3>SMTP Configuration:</h3>";
echo "Host: " . SMTP_HOST . "<br>";
echo "Port: " . SMTP_PORT . "<br>";
echo "User: " . SMTP_USER . "<br>";
echo "From: " . SMTP_FROM . "<br>";
echo "Admin Email: " . ADMIN_EMAIL . "<br>";

echo "<hr>";

echo "<h3>Sending Test Email...</h3>";

$result = sendTestEmail();

if ($result) {
    echo "<p style='color: green; font-size: 20px;'>✅ Test email sent successfully!</p>";
    echo "<p>Check your inbox at: <strong>" . ADMIN_EMAIL . "</strong></p>";
} else {
    echo "<p style='color: red; font-size: 20px;'>❌ Failed to send test email.</p>";
    echo "<p>Check error logs at: <code>/patchwebsite/logs/error.log</code></p>";
}

echo "<hr>";
echo "<p><a href='" . SITE_URL . "'>Back to Website</a></p>";
?>