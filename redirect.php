<?php
// redirect.php - Processing / Redirect Page
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting - VinUnified</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #6C63FF, #00B4D8);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .redirect-card {
            background: white;
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            max-width: 500px;
            margin: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #E8E6FF;
            border-top-color: #6C63FF;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        h2 { color: #03045E; margin-bottom: 10px; }
        p { color: #6C757D; margin-bottom: 20px; }
        .btn {
            display: inline-block;
            background: #6C63FF;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            margin-top: 20px;
        }
    </style>
    <meta http-equiv="refresh" content="3;url=<?php echo $_SESSION['payment_url'] ?? 'index.php'; ?>">
</head>
<body>
    <div class="redirect-card">
        <div class="spinner"></div>
        <h2>Processing Your Request</h2>
        <p>Please wait, you will be redirected to our secure payment page.</p>
        <p><small>If not redirected automatically, click the button below.</small></p>
        <a href="<?php echo $_SESSION['payment_url'] ?? 'index.php'; ?>" class="btn">Go to Payment →</a>
    </div>
</body>
</html>
<?php
// Clear session data after redirect
session_destroy();
?>