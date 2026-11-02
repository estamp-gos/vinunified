<?php
// sample-report.php - Sample Report Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Report - VinUnified</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .sample-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }
        .pdf-viewer {
            width: 100%;
            height: 80vh;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
            transition: 0.3s;
        }
        .back-btn:hover {
            background: #4A42D1;
        }
    </style>
</head>
<body style="background: #F8F9FF;">
    <div class="sample-container">
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Home</a>
        <h1 style="color: #03045E; margin-bottom: 20px;">Sample Vehicle History Report</h1>
        <p style="margin-bottom: 20px; color: #6C757D;">This is a sample report. Actual reports contain complete vehicle history data from worldwide sources.</p>
        
        <!-- PDF Viewer -->
        <iframe src="assets/pdf/sample-report.pdf" class="pdf-viewer"></iframe>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="index.php#multiStepForm" class="btn-sample" style="display: inline-flex;">Get Your Report Now →</a>
        </div>
    </div>
</body>
</html>