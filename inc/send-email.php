<?php
// inc/send-email.php - Simplified (Only Admin Notification to rmoto7817@gmail.com)

require_once __DIR__ . '/config.php';

function sendAdminNotification($data) {
    $to = ADMIN_EMAIL; // rmoto7817@gmail.com
    
    $subject = "🔔 New VIN Check Request - VinUnified";
    
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
                    ⚠️ Customer will be redirected to <strong>Freemius</strong> for payment.
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
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: VinUnified <support@vinunified.store>\r\n";
    $headers .= "Reply-To: support@vinunified.store\r\n";
    
    return mail($to, $subject, $message, $headers);
}
?>