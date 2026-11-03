<?php
// inc/send-email.php - Admin notification for entry-form submissions.
//
// Why this was changed: the old version used PHP mail() only, with no
// envelope sender, no encoded subject and no record of a failed send.
// mail() only reports whether the local mail server accepted the message,
// not whether it was delivered, so a failed send was invisible to the
// visitor (who was still redirected to checkout) and to the admin.
// This version:
//  1. Uses authenticated SMTP via PHPMailer when SMTP settings are present
//     (the reliable route on a live host, and the one Gmail trusts).
//  2. Otherwise falls back to mail(), with an encoded subject, an envelope
//     sender (-f) matching the From domain, and Reply-To set to the
//     customer so the admin can reply directly.
//  3. On any failure, saves the full submission to logs/submissions.log
//     (protected by logs/.htaccess) so no entry is ever lost silently,
//     and logs the reason to logs/error.log.

require_once __DIR__ . '/config.php';

function logSubmissionFallback($data) {
    $logFile = __DIR__ . '/../logs/submissions.log';
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $record = array_merge(
        ['submitted_at' => date('Y-m-d H:i:s')],
        is_array($data) ? $data : []
    );
    // Never log a password, even if one is ever added to the form data.
    unset($record['password'], $record['SMTP_PASS'], $record['smtp_pass']);
    @file_put_contents(
        $logFile,
        json_encode($record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

function sendAdminNotification($data) {
    $to = ADMIN_EMAIL;

    $subject = "🔔 New VIN Check Request - VinUnified";
    // Encode the emoji subject properly; a raw UTF-8 emoji in the subject
    // header is rejected or mangled by some mail servers.
    if (function_exists('mb_encode_mimeheader')) {
        $subject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");
    }

    $message = "
    <html>
    <head>
        <title>New VIN Check Request</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #6C63FF, #00B4D8); padding: 20px; border-radius: 12px 12px 0 0; color: white; text-align: center; }
            .content { padding: 20px; }
            .field { padding: 8px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; }
            .label { font-weight: 600; color: #555; }
            .value { color: #1a1a2e; }
            .footer { text-align: center; padding: 15px; font-size: 12px; color: #888; border-top: 1px solid #eee; margin-top: 20px; }
            .badge { display: inline-block; background: #28A745; color: white; padding: 4px 12px; border-radius: 50px; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2 style='margin:0;'>📋 New Form Submission</h2>
                <p style='margin:5px 0 0; opacity:0.8;'>VinUnified Vehicle History Report</p>
            </div>
            <div class='content'>
                <table style='width:100%; border-collapse:collapse;'>
                    <tr><td class='field'><span class='label'>👤 Full Name</span><span class='value'>{$data['full_name']}</span></td></tr>
                    <tr><td class='field'><span class='label'>📧 Email</span><span class='value'>{$data['email']}</span></td></tr>
                    <tr><td class='field'><span class='label'>📱 Phone</span><span class='value'>{$data['phone']}</span></td></tr>
                    <tr><td class='field'><span class='label'>🔢 VIN</span><span class='value'><strong>{$data['vin']}</strong></span></td></tr>
                    <tr><td class='field'><span class='label'>🚗 License Plate</span><span class='value'>{$data['license_plate']}</span></td></tr>
                    <tr><td class='field'><span class='label'>🚙 Vehicle Type</span><span class='value'>{$data['vehicle_type']}</span></td></tr>
                    <tr><td class='field'><span class='label'>📦 Package</span><span class='value'><span class='badge'>" . ucfirst($data['package']) . "</span></span></td></tr>
                    <tr><td class='field'><span class='label'>🌍 Country</span><span class='value'>{$data['country']}</span></td></tr>
                    <tr><td class='field'><span class='label'>🖥️ IP Address</span><span class='value'>{$_SERVER['REMOTE_ADDR']}</span></td></tr>
                    <tr><td class='field'><span class='label'>📅 Submitted</span><span class='value'>" . date('Y-m-d H:i:s') . "</span></td></tr>
                </table>
                <div style='background: #FFF3CD; padding: 12px; border-radius: 8px; margin-top: 20px; text-align: center;'>
                    ⚠️ Customer will be redirected to <strong>Gumroad</strong> for payment.
                </div>
            </div>
            <div class='footer'>
                <p style='margin:0;'>This is an automated notification from VinUnified.</p>
                <p style='margin:5px 0 0; color:#6C63FF;'>One VIN. Global History.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $from = 'support@vinunified.store';
    $replyTo = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)
        ? $data['email']
        : $from;

    // --- Route 1: authenticated SMTP (preferred on the live host) ---
    $smtpHost = getenv('SMTP_HOST') ?: (defined('SMTP_HOST') ? SMTP_HOST : '');
    $smtpUser = getenv('SMTP_USER') ?: (defined('SMTP_USER') ? SMTP_USER : '');
    $smtpPass = getenv('SMTP_PASS') ?: (defined('SMTP_PASS') ? SMTP_PASS : '');
    if (!empty($smtpHost) && !empty($smtpUser) && !empty($smtpPass)) {
        $smtpPort = getenv('SMTP_PORT') ?: (defined('SMTP_PORT') ? SMTP_PORT : 465);
        $smtpEncryption = getenv('SMTP_ENCRYPTION') ?: (defined('SMTP_ENCRYPTION') ? SMTP_ENCRYPTION : 'ssl');
        $smtpFrom = getenv('SMTP_FROM') ?: (defined('SMTP_FROM') ? SMTP_FROM : $from);
        require_once __DIR__ . '/../PHPMailer/src/Exception.php';
        require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            if ($smtpEncryption === '' || strtolower($smtpEncryption) === 'none') {
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            } else {
                $mail->SMTPSecure = $smtpEncryption;
            }
            $mail->Port = (int) $smtpPort;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($smtpFrom, 'VinUnified');
            $mail->addAddress($to);
            $mail->addReplyTo($replyTo);
            $mail->isHTML(true);
            $mail->Subject = "🔔 New VIN Check Request - VinUnified";
            $mail->Body = $message;
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Never log the SMTP password; PHPMailer's message does not contain it.
            $reason = $mail->ErrorInfo ? $mail->ErrorInfo : $e->getMessage();
            if (function_exists('logError')) {
                logError("SMTP admin email failed for: " . ($data['email'] ?? '') . " - " . $reason);
            }
            logSubmissionFallback($data);
            return false;
        }
    }

    // --- Route 2: mail() fallback, with a proper envelope sender ---
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: VinUnified <{$from}>\r\n";
    $headers .= "Reply-To: {$replyTo}\r\n";

    error_clear_last();
    $sent = @mail($to, $subject, $message, $headers, "-f {$from}");
    if (!$sent) {
        // Some hosts reject the -f parameter; retry without it before giving up.
        $firstError = error_get_last();
        error_clear_last();
        $sent = @mail($to, $subject, $message, $headers);
    }
    if (!$sent) {
        $lastError = error_get_last();
        $reason = $lastError ? ($lastError['message'] ?? 'mail() returned false') : 'mail() returned false';
        if (isset($firstError) && $firstError) {
            $reason = ($firstError['message'] ?? '') . ' | retry: ' . $reason;
        }
        if (function_exists('logError')) {
            logError("Failed to send admin email for: " . ($data['email'] ?? '') . " - " . $reason);
        }
        logSubmissionFallback($data);
        return false;
    }
    return true;
}
?>
