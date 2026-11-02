<?php
// /patchwebsite/customerpanel/login.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// If already logged in as customer, redirect to dashboard
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer') {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Customer Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - PunchyArts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/icon.png">
    <style>
        body {
            background: #0D0F12;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .customer-login-card {
            background: #181B20;
            border: 1px solid #2A2F38;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 420px;
            width: 100%;
        }
        .customer-login-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .customer-login-card .logo small {
            color: #A0A5B5;
            display: block;
        }
        .customer-login-card .form-control {
            background: #111418 !important;
            border: 1px solid #2A2F38 !important;
            color: #FFFFFF !important;
            padding: 12px 16px;
            border-radius: 12px;
        }
        .customer-login-card .form-control:focus {
            border-color: #00F0FF !important;
            box-shadow: 0 0 20px rgba(0, 240, 255, 0.15) !important;
        }
        .customer-login-card .btn-login {
            background: #00F0FF;
            color: #0D0F12;
            font-weight: 600;
            padding: 14px;
            border-radius: 50px;
            border: none;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .customer-login-card .btn-login:hover {
            background: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(0, 240, 255, 0.3);
        }
        .customer-login-card .form-label {
            color: #A0A5B5;
            font-weight: 500;
            font-size: 14px;
        }
        .customer-login-card .btn-register {
            background: transparent;
            color: #00F0FF;
            border: 2px solid #00F0FF;
            font-weight: 600;
            padding: 12px;
            border-radius: 50px;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .customer-login-card .btn-register:hover {
            background: #00F0FF;
            color: #0D0F12;
        }
    </style>
</head>
<body>
    <div class="customer-login-card">
        <div class="logo">
            <img src="<?php echo SITE_URL; ?>assets/images/logo-horizontal.png" alt="PunchyArts" height="45">
            <small>Customer Login</small>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="you@example.com" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="text-center mt-3">
            <span class="text-muted">Don't have an account?</span>
        </div>
        <a href="<?php echo SITE_URL; ?>register.php" class="btn-register mt-2 text-center">Create Account</a>
        
        <p class="text-center mt-3">
            <a href="<?php echo SITE_URL; ?>" class="text-muted small">&larr; Back to Website</a>
        </p>
    </div>
</body>
</html>