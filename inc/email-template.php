<?php
function getEmailTemplate($userName, $vin, $package, $amount, $status = 'success') {
    $logoUrl = 'https://vinunified.store/assets/images/vinunified-nav.png';
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>VinUnified - Vehicle Report</title>
    </head>
    <body style="margin:0; padding:20px; background-color:#F8F9FF; font-family: Arial, sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:white; border-radius:20px; overflow:hidden;">
            <tr>
                <td style="background: linear-gradient(135deg, #6C63FF, #00B4D8); padding:30px; text-align:center;">
                    <img src="' . $logoUrl . '" alt="VinUnified" style="height:50px;">
                    <h2 style="color:white; margin:15px 0 0;">Vehicle History Report</h2>
                </td>
            </tr>
            <tr>
                <td style="padding:30px;">
                    <h3 style="color:#03045E;">Hello ' . $userName . ',</h3>
                    <p>Thank you for choosing <strong>VinUnified</strong>.</p>
                    <p>Your vehicle history report for <strong>VIN: ' . $vin . '</strong> with <strong>' . ucfirst($package) . ' Package</strong> is ready.</p>
                    
                    <table width="100%" cellpadding="10" cellspacing="0" style="background:#F8F9FF; border-radius:12px; margin:20px 0;">
                        <tr><td style="color:#6C757D;">VIN Number:</td><td style="font-weight:600;">' . $vin . '</td></tr>
                        <tr><td style="color:#6C757D;">Package:</td><td style="font-weight:600;">' . ucfirst($package) . '</td></tr>
                        <tr><td style="color:#6C757D;">Amount Paid:</td><td style="font-weight:600;">' . $amount . '</td></tr>
                        <tr><td style="color:#6C757D;">Status:</td><td style="color:#28A745; font-weight:600;">Paid & Delivered</td></tr>
                    </table>
                    
                    <div style="text-align:center; margin:30px 0;">
                        <a href="https://vinunified.store/assets/pdf/report-sample.pdf" style="background:#6C63FF; color:white; padding:12px 30px; text-decoration:none; border-radius:50px; display:inline-block;">📄 Download Report</a>
                    </div>
                    
                    <hr style="border:none; border-top:1px solid #E5E7EB; margin:20px 0;">
                    
                    <div style="background:#FFF3CD; padding:15px; border-radius:12px; margin:20px 0;">
                        <p style="margin:0; color:#856404;"><strong>⚠️ Not satisfied?</strong> Contact us within 14 days for free updated report or full refund.</p>
                    </div>
                    
                    <div style="text-align:center; margin-top:30px;">
                        <a href="mailto:support@vinunified.store?subject=Cancel Order - ' . $vin . '" style="color:#6C757D; margin-right:20px;">❌ Cancel Order</a>
                        <a href="mailto:support@vinunified.store?subject=Refund Request - ' . $vin . '" style="color:#6C63FF;">💰 Request Refund</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="background:#03045E; padding:20px; text-align:center; color:white; font-size:12px;">
                    <p>© 2026 VinUnified | One VIN. Global History.</p>
                    <p><a href="mailto:support@vinunified.store" style="color:#6C63FF;">support@vinunified.store</a></p>
                </td>
            </tr>
        </table>
    </body>
    </html>';
    
    return $html;
}
?>