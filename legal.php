<?php
// legal.php - Privacy Policy + Terms + Refund (One Page)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal - VinUnified</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .legal-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }
        .legal-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .legal-section h2 {
            color: #6C63FF;
            margin-bottom: 20px;
            border-bottom: 2px solid #E8E6FF;
            padding-bottom: 10px;
        }
        .legal-section h3 {
            color: #03045E;
            margin: 20px 0 10px;
        }
        .legal-section p, .legal-section li {
            color: #6C757D;
            line-height: 1.6;
        }
        .legal-section ul {
            padding-left: 20px;
            margin: 15px 0;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #6C63FF;
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .back-btn:hover { background: #4A42D1; }
        .freemius-badge {
            display: inline-block;
            background: #6C63FF;
            color: white;
            padding: 2px 12px;
            border-radius: 50px;
            font-size: 12px;
        }
    </style>
</head>
<body style="background: #F8F9FF;">
    <div class="legal-container">
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Home</a>
        
        <!-- Privacy Policy -->
        <div class="legal-section">
            <h2>Privacy Policy</h2>
            <p><strong>Last Updated:</strong> July 31, 2026</p>
            <p>At VinUnified, we value your privacy. This policy explains how we collect, use, and protect your information.</p>
            
            <h3>Information We Collect</h3>
            <ul>
                <li>Full Name</li>
                <li>Email Address</li>
                <li>Phone Number</li>
                <li>VIN Number or License Plate</li>
                <li>Vehicle Type</li>
                <li>IP Address (for security)</li>
            </ul>
            
            <h3>How We Use Your Information</h3>
            <ul>
                <li>To generate and deliver vehicle history reports</li>
                <li>To process payments through our partner <strong>Freemius</strong></li>
                <li>To communicate with you about your order</li>
                <li>To prevent fraud and ensure security</li>
            </ul>
            
            <h3>Data Retention</h3>
            <p>Your data is automatically deleted after 30 days. We do not share, sell, or market your personal information.</p>
            
            <h3>Third-Party Services</h3>
            <p>Payments are processed securely by <strong>Freemius</strong>. We do not store payment information on our servers.</p>
        </div>
        
        <!-- Terms & Conditions -->
        <div class="legal-section">
            <h2>Terms & Conditions</h2>
            <p><strong>Last Updated:</strong> July 31, 2026</p>
            <p>By using VinUnified.store, you agree to these Terms & Conditions.</p>
            
            <h3>Service Description</h3>
            <p>VinUnified provides vehicle history reports based on worldwide digital data available from public sources. Reports include accident history, title records, odometer readings, recalls, and market value where available.</p>
            
            <h3>Disclaimer</h3>
            <p>While we strive for accuracy, we do not guarantee 100% completeness. Reports are generated from available data sources. We recommend using this report as one factor in your decision-making process.</p>
            
            <h3>Limitation of Liability</h3>
            <p>VinUnified shall not be liable for any indirect, incidental, or consequential damages arising from the use of our service. Our total liability shall not exceed the amount paid for the report.</p>
        </div>
        
        <!-- Refund Policy -->
        <div class="legal-section">
            <h2>Refund Policy</h2>
            <p><strong>Last Updated:</strong> July 31, 2026</p>
            
            <h3>14-Day Money-Back Guarantee</h3>
            <p>Customer satisfaction is our highest priority. We offer a transparent 14-day refund policy from the date of your purchase.</p>
            
            <h3>Eligibility for Refunds</h3>
            <p>You are eligible for a full refund or a free updated report under the following circumstances:</p>
            <ul>
                <li><strong>No Data Available / Technical Issue:</strong> If our system is unable to generate or retrieve records for your provided VIN or license plate.</li>
                <li><strong>Data Inaccuracy or Incompleteness:</strong> If the generated report contains verifiable inaccuracies or missing key vehicle history records, and our team cannot provide a refreshed/updated report.</li>
                <li><strong>Duplicate Charge:</strong> If you were accidentally charged more than once for the same transaction.</li>
                <li><strong>Delivery Delays:</strong> If your report is not delivered within the guaranteed timeframe and our support team does not resolve the issue.</li>
            </ul>
            
            <h3>Resolution & Free Report Updates</h3>
            <p>If you have any questions or notice incomplete information on your report, contact us and we will promptly supply a refreshed report with expanded data sources at no additional charge. If you remain unsatisfied, we will issue a complete refund.</p>
            
            <h3>How to Request a Refund</h3>
            <p>Email us at <strong>support@vinunified.store</strong> with your order reference number, email address, and VIN. Our customer support team will review and process your request within 24-48 hours. Approved refunds are credited back to your original payment method via Freemius.</p>
            
            <div style="margin-top: 20px; text-align: center;">
                <span class="freemius-badge">🔒 Payments by Freemius</span>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn-sample" style="display: inline-flex;">← Back to Home</a>
        </div>
    </div>
</body>
</html>