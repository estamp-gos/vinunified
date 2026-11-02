<?php
// /patchwebsite/includes/mail.php

// ============================================
// EMAIL FUNCTIONS - PROFESSIONAL TEMPLATES
// ============================================

function sendMail($to, $subject, $message, $from = null, $fromName = null) {
    $from = $from ?? SMTP_FROM;
    $fromName = $fromName ?? SMTP_FROM_NAME;
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $fromName <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    $fullMessage = '<html><body>' . $message . '</body></html>';
    $result = mail($to, $subject, $fullMessage, $headers);
    error_log("MAIL: Sent to $to - Subject: $subject - Result: " . ($result ? 'SUCCESS' : 'FAILED'));
    return $result;
}

function sendMailSMTP($to, $subject, $message, $from = null, $fromName = null) {
    $from = $from ?? SMTP_FROM;
    $fromName = $fromName ?? SMTP_FROM_NAME;
    
    error_log("SMTP: Attempting to send to $to - Subject: $subject");
    
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port = SMTP_PORT;
            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($from, $fromName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = strip_tags($message);
            $result = $mail->send();
            error_log("SMTP: PHPMailer SUCCESS to $to");
            return true;
        } catch (Exception $e) {
            error_log("SMTP: PHPMailer ERROR: " . $e->getMessage());
            return sendMail($to, $subject, $message, $from, $fromName);
        }
    }
    
    error_log("SMTP: PHPMailer not found, using mail()");
    return sendMail($to, $subject, $message, $from, $fromName);
}

/**
 * Get professional email wrapper
 */
function getEmailWrapper($content) {
    $logoUrl = SITE_URL . 'assets/images/logo-horizontal.png';
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . SITE_NAME . '</title>
        <style>
            body { margin: 0; padding: 20px; background: #0A0C0F; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
            .email-wrapper { max-width: 580px; margin: 0 auto; background: #111418; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.06); }
            .email-header { background: #0D0F12; padding: 24px 32px 16px; text-align: center; border-bottom: 2px solid #00F0FF; }
            .email-header img { max-height: 40px; width: auto; }
            .email-header h1 { color: #FFFFFF; font-size: 18px; font-weight: 700; margin: 6px 0 0; }
            .email-header p { color: #A0A5B5; font-size: 13px; margin: 2px 0 0; }
            .email-body { padding: 28px 32px; color: #E0E0E0; }
            .email-body h2 { color: #00F0FF; font-size: 20px; font-weight: 700; margin: 0 0 14px; }
            .email-body p { color: #A0A5B5; font-size: 14px; line-height: 1.7; margin: 0 0 12px; }
            .email-body .highlight-box { background: #0A0C0F; border-left: 3px solid #00F0FF; padding: 14px 18px; border-radius: 6px; margin: 12px 0; }
            .email-body .highlight-box p { margin: 0; color: #E0E0E0; }
            .email-body table { width: 100%; border-collapse: collapse; margin: 12px 0; }
            .email-body table td { padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.04); color: #A0A5B5; font-size: 13px; }
            .email-body table td.label { color: #FFFFFF; font-weight: 600; width: 35%; }
            .email-body table td a { color: #00F0FF; text-decoration: none; }
            .btn-cyan { display: inline-block; background: #00F0FF; color: #0D0F12; font-weight: 700; padding: 10px 28px; border-radius: 50px; text-decoration: none; font-size: 13px; }
            .btn-cyan:hover { background: #FFFFFF; }
            .btn-outline { display: inline-block; background: transparent; color: #00F0FF; font-weight: 600; padding: 8px 24px; border-radius: 50px; text-decoration: none; font-size: 13px; border: 2px solid #00F0FF; }
            .btn-outline:hover { background: #00F0FF; color: #0D0F12; }
            .file-box { background: #0A0C0F; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 12px 16px; margin: 10px 0; }
            .file-box .file-row { display: flex; align-items: center; gap: 12px; }
            .file-box .file-row img { max-width: 120px; max-height: 100px; border-radius: 4px; object-fit: cover; }
            .file-box .file-row .file-icon { font-size: 28px; color: #00F0FF; }
            .file-box .file-row .file-info { flex: 1; }
            .file-box .file-row .file-info .fname { color: #FFFFFF; font-size: 13px; font-weight: 500; }
            .file-box .file-row .file-info .flink { color: #00F0FF; font-size: 12px; text-decoration: none; background: rgba(0,240,255,0.08); padding: 2px 12px; border-radius: 4px; display: inline-block; margin-top: 4px; }
            .email-body ul { padding-left: 18px; color: #A0A5B5; font-size: 14px; line-height: 1.8; }
            .email-body ul li::marker { color: #00F0FF; }
            .email-footer { background: #0D0F12; padding: 16px 32px; text-align: center; border-top: 1px solid rgba(255,255,255,0.04); }
            .email-footer p { color: #4A4F5A; font-size: 11px; margin: 0; line-height: 1.6; }
            .email-footer a { color: #00F0FF; text-decoration: none; }
            .email-footer .social a { color: #4A4F5A; font-size: 16px; margin: 0 6px; text-decoration: none; }
            .email-footer .social a:hover { color: #00F0FF; }
            @media (max-width: 480px) {
                .email-header { padding: 18px 16px; }
                .email-body { padding: 20px 16px; }
                .email-footer { padding: 12px 16px; }
                .email-body h2 { font-size: 17px; }
                .file-box .file-row { flex-wrap: wrap; }
                .file-box .file-row img { max-width: 100%; }
            }
        </style>
    </head>
    <body>
        <div class="email-wrapper">
            ' . $content . '
        </div>
    </body>
    </html>';
}

/**
 * Get email header
 */
function getEmailHeader() {
    $logoUrl = SITE_URL . 'assets/images/logo-horizontal.png';
    return '
    <div class="email-header">
        <img src="' . $logoUrl . '" alt="' . SITE_NAME . '">
        <h1>' . SITE_NAME . '</h1>
        <p>' . SITE_TAGLINE . '</p>
    </div>';
}

/**
 * Get email footer
 */
function getEmailFooter() {
    return '
    <div class="email-footer">
        <p>&copy; ' . date('Y') . ' ' . SITE_NAME . '. All rights reserved.</p>
        <p>' . SITE_ADDRESS . ' | ' . SITE_PHONE . '</p>
        <p><a href="' . SITE_URL . '">' . SITE_URL . '</a></p>
        <div class="social">
            <a href="' . FACEBOOK_URL . '" target="_blank">f</a>
            <a href="' . INSTAGRAM_URL . '" target="_blank">i</a>
            <a href="' . YOUTUBE_URL . '" target="_blank">y</a>
        </div>
    </div>';
}

/**
 * Format file attachment for email
 */
function formatFileAttachment($file) {
    if (!$file || !isset($file['name'])) return '';
    
    $fileUrl = $file['url'] ?? '#';
    $fileName = htmlspecialchars($file['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    
    $html = '<div class="file-box">';
    $html .= '<div class="file-row">';
    if ($isImage && !empty($fileUrl) && $fileUrl !== '#') {
        $html .= '<img src="' . $fileUrl . '" alt="' . $fileName . '">';
    } else {
        $html .= '<div class="file-icon">📄</div>';
    }
    $html .= '<br><div class="file-info">';
    $html .= '<div class="fname">' . $fileName . '</div>';
    $html .= '<a href="' . $fileUrl . '" class="flink">⬇ Download</a>';
    $html .= '</div></div></div>';
    
    return $html;
}

// ============================================
// EMAIL FUNCTIONS
// ============================================

function sendContactMessageAdmin($name, $email, $subject, $message, $file = null) {
    $subject = "Contact Form: $subject";
    $content = getEmailHeader() . '
    <div class="email-body">
        <h2>📩 New Contact Message</h2>
        <p>A new message has been received from your website.</p>
        
        <table>
            <tr><td class="label">Name</td><td>' . htmlspecialchars($name) . '</td></tr>
            <tr><td class="label">Email</td><td><a href="mailto:' . $email . '">' . $email . '</a></td></tr>
            <tr><td class="label">Subject</td><td>' . htmlspecialchars($subject) . '</td></tr>
        </table>
        
        <div class="highlight-box">
            <p><strong>Message:</strong><br>' . nl2br(htmlspecialchars($message)) . '</p>
        </div>';
    
    if ($file) {
        $content .= formatFileAttachment($file);
    }
    
    $content .= '
        <p style="margin-top:16px;">
            <a href="' . ADMIN_URL . '" class="btn-cyan">Go to Admin Panel</a>
        </p>
    </div>' . getEmailFooter();
    
    return sendMailSMTP(ADMIN_EMAIL, $subject, getEmailWrapper($content));
}

function sendContactMessageCustomer($name, $email, $subject, $message, $file = null) {
    $subject = "Copy: Your Message to PunchyArts";
    $content = getEmailHeader() . '
    <div class="email-body">
        <h2>📧 Thank You for Contacting Us!</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>This is a copy of your message. We will respond within 24 hours.</p>
        
        <div style="background:#0A0C0F; border-radius:6px; padding:14px 16px; margin:12px 0; border:1px solid rgba(255,255,255,0.04);">
            <p style="color:#00F0FF; font-weight:600; margin:0 0 4px;">Your Message:</p>
            <p style="color:#A0A5B5; margin:0;"><strong>Subject:</strong> ' . htmlspecialchars($subject) . '</p>
            <div style="background:#0D0F12; padding:10px 14px; border-radius:4px; margin-top:6px;">
                <p style="color:#A0A5B5; margin:0;">' . nl2br(htmlspecialchars($message)) . '</p>
            </div>
        </div>';
    
    if ($file) {
        $content .= formatFileAttachment($file);
    }
    
    $content .= '
        <p>We appreciate your interest in PunchyArts!</p>
        <p style="margin-top:16px;">
            <a href="' . SITE_URL . '" class="btn-outline">Visit Website</a>
        </p>
        <p>Best regards,<br><strong style="color:#FFFFFF;">Team PunchyArts</strong></p>
    </div>' . getEmailFooter();
    
    return sendMailSMTP($email, $subject, getEmailWrapper($content));
}

function sendWelcomeEmail($email, $name) {
    $subject = "Welcome to PunchyArts! 🎨";
    $content = getEmailHeader() . '
    <div class="email-body">
        <h2>Welcome to PunchyArts!</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>Thank you for joining PunchyArts! We\'re excited to have you on board.</p>
        <p>With your account, you can:</p>
        <ul>
            <li>Browse our professional design services</li>
            <li>Place orders for embroidery digitizing, vector tracing, character art, and custom patches</li>
            <li>Track your order status in real-time</li>
            <li>Download production files directly from your dashboard</li>
        </ul>
        <p style="margin-top:16px;">
            <a href="' . CUSTOMER_URL . '" class="btn-cyan">Go to Dashboard</a>
        </p>
        <p>Best regards,<br><strong style="color:#FFFFFF;">Team PunchyArts</strong></p>
    </div>' . getEmailFooter();
    
    return sendMailSMTP($email, $subject, getEmailWrapper($content));
}

function sendOrderConfirmationAdmin($email, $name, $orderNumber, $serviceType, $orderId) {
    $subject = "New Order #$orderNumber - PunchyArts";
    $content = getEmailHeader() . '
    <div class="email-body">
        <h2>🆕 New Order Received!</h2>
        <p>A new order has been placed by <strong style="color:#FFFFFF;">' . htmlspecialchars($name) . '</strong>.</p>
        
        <table>
            <tr><td class="label">Order #</td><td style="color:#00F0FF; font-weight:600;">' . $orderNumber . '</td></tr>
            <tr><td class="label">Customer</td><td>' . htmlspecialchars($name) . '</td></tr>
            <tr><td class="label">Email</td><td><a href="mailto:' . $email . '">' . $email . '</a></td></tr>
            <tr><td class="label">Service</td><td>' . getServiceName($serviceType) . '</td></tr>
        </table>
        
        <p style="margin-top:16px;">
            <a href="' . ADMIN_URL . 'order-details.php?id=' . $orderId . '" class="btn-cyan">View Order</a>
        </p>
    </div>' . getEmailFooter();
    
    return sendMailSMTP(ADMIN_EMAIL, $subject, getEmailWrapper($content));
}

function sendNewOrderNotification($orderNumber, $customerName, $customerEmail, $serviceType, $orderId) {
    return sendOrderConfirmationAdmin($customerEmail, $customerName, $orderNumber, $serviceType, $orderId);
}

function sendOrderConfirmation($email, $name, $orderNumber, $serviceType) {
    $subject = "Order Confirmation #$orderNumber - PunchyArts";
    $content = getEmailHeader() . '
    <div class="email-body">
        <h2>✅ Order Confirmed!</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>Your order <strong style="color:#FFFFFF;">#' . $orderNumber . '</strong> for <strong style="color:#FFFFFF;">' . getServiceName($serviceType) . '</strong> has been submitted successfully.</p>
        
        <div class="highlight-box">
            <p>We will review your order and get back to you shortly.</p>
        </div>
        
        <p style="margin-top:16px;">
            <a href="' . CUSTOMER_URL . '" class="btn-cyan">View Orders</a>
        </p>
        <p>Thank you for choosing PunchyArts!</p>
        <p>Best regards,<br><strong style="color:#FFFFFF;">Team PunchyArts</strong></p>
    </div>' . getEmailFooter();
    
    return sendMailSMTP($email, $subject, getEmailWrapper($content));
}

function sendProductionFilesReady($email, $name, $orderNumber) {
    $subject = "Production Files Ready #$orderNumber - PunchyArts";
    $content = getEmailHeader() . '
    <div class="email-body">
        <h2>📁 Production Files Ready!</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>Great news! The production files for your order <strong style="color:#FFFFFF;">#' . $orderNumber . '</strong> are now available.</p>
        
        <p style="margin-top:16px;">
            <a href="' . CUSTOMER_URL . '" class="btn-cyan">Download Files</a>
        </p>
        <p>Best regards,<br><strong style="color:#FFFFFF;">Team PunchyArts</strong></p>
    </div>' . getEmailFooter();
    
    return sendMailSMTP($email, $subject, getEmailWrapper($content));
}

function sendShippingNotification($email, $name, $orderNumber, $trackingNumber) {
    $subject = "Your Order #$orderNumber Has Shipped! 📦";
    $content = getEmailHeader() . '
    <div class="email-body">
        <h2>🚚 Order Shipped!</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>Your order <strong style="color:#FFFFFF;">#' . $orderNumber . '</strong> has been shipped!</p>
        
        <div class="highlight-box">
            <p><strong>Tracking Number:</strong> ' . $trackingNumber . '</p>
        </div>
        
        <p style="margin-top:16px;">
            <a href="' . CUSTOMER_URL . '" class="btn-cyan">Track Order</a>
        </p>
        <p>Best regards,<br><strong style="color:#FFFFFF;">Team PunchyArts</strong></p>
    </div>' . getEmailFooter();
    
    return sendMailSMTP($email, $subject, getEmailWrapper($content));
}

function sendPasswordResetEmail($email, $name, $resetLink) {
    $subject = "Password Reset - PunchyArts";
    $content = getEmailHeader() . '
    <div class="email-body">
        <h2>🔐 Password Reset</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>We received a request to reset your password. Click the button below:</p>
        
        <p style="margin:16px 0;">
            <a href="' . $resetLink . '" class="btn-cyan">Reset Password</a>
        </p>
        
        <div class="highlight-box">
            <p>⚠️ This link expires in 1 hour.</p>
        </div>
        
        <p>If you didn\'t request this, please ignore this email.</p>
        <p>Best regards,<br><strong style="color:#FFFFFF;">Team PunchyArts</strong></p>
    </div>' . getEmailFooter();
    
    return sendMailSMTP($email, $subject, getEmailWrapper($content));
}

function sendTestEmail() {
    $subject = "Test Email - PunchyArts";
    $content = getEmailHeader() . '
    <div class="email-body">
        <h2>✅ Test Email</h2>
        <p>This is a test email from PunchyArts.</p>
        <div class="highlight-box">
            <p>If you received this, your email system is working correctly!</p>
        </div>
        <p><strong>Time:</strong> ' . date('Y-m-d H:i:s') . '</p>
        <p><strong>SMTP Host:</strong> ' . SMTP_HOST . '</p>
        <p><strong>SMTP User:</strong> ' . SMTP_USER . '</p>
        <p><strong>Admin Email:</strong> ' . ADMIN_EMAIL . '</p>
    </div>' . getEmailFooter();
    
    return sendMailSMTP(ADMIN_EMAIL, $subject, getEmailWrapper($content));
}
?>