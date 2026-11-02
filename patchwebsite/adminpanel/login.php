<?php
// /patchwebsite/adminpanel/login.php

// ============================================
// ENABLE ERROR LOGGING
// ============================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create log file if not exists
$logFile = __DIR__ . '/../logs/admin_login.log';
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

// Custom error logger
function logAdminError($message, $data = null) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $log = "[$timestamp] ERROR: $message";
    if ($data !== null) {
        $log .= " | Data: " . print_r($data, true);
    }
    $log .= PHP_EOL;
    file_put_contents($logFile, $log, FILE_APPEND);
}

// Log that login page was accessed
logAdminError("Admin login page accessed", [
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
]);

// ============================================
// LOAD CONFIGURATION - FIXED PATHS
// ============================================
try {
    require_once __DIR__ . '/../includes/config.php';
    logAdminError("Config loaded successfully");
} catch (Exception $e) {
    logAdminError("Config loading failed", $e->getMessage());
    die("Config loading failed. Check logs.");
}

try {
    require_once __DIR__ . '/../includes/functions.php';
    logAdminError("Functions loaded successfully");
} catch (Exception $e) {
    logAdminError("Functions loading failed", $e->getMessage());
    die("Functions loading failed. Check logs.");
}

try {
    require_once __DIR__ . '/../includes/auth.php';
    logAdminError("Auth loaded successfully");
} catch (Exception $e) {
    logAdminError("Auth loading failed", $e->getMessage());
    die("Auth loading failed. Check logs.");
}

// ============================================
// CHECK ADMIN SESSION
// ============================================
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    logAdminError("User already logged in as admin, redirecting to dashboard", [
        'user_id' => $_SESSION['user_id'] ?? 'unknown'
    ]);
    header('Location: dashboard.php');
    exit;
}

$error = '';

// ============================================
// HANDLE LOGIN POST
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logAdminError("Login form submitted");
    
    try {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        logAdminError("Login attempt", ['email' => $email]);

        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
            logAdminError("Login failed: Empty fields");
        } else {
            $db = getDB();
            logAdminError("Database connection successful for login");
            
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                logAdminError("Admin user found", ['user_id' => $user['id'], 'name' => $user['name']]);
                
                if ($password === $user['password']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    logAdminError("Login successful", ['user_id' => $user['id'], 'email' => $email]);
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                    logAdminError("Login failed: Incorrect password", ['email' => $email]);
                }
            } else {
                $error = 'Invalid email or password.';
                logAdminError("Login failed: No admin user found", ['email' => $email]);
            }
        }
    } catch (Exception $e) {
        logAdminError("Login exception", $e->getMessage());
        $error = 'An error occurred. Please try again.';
    }
}

// ============================================
// DISPLAY LOGIN PAGE
// ============================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PunchyArts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/icon.png">
    <style>
        body {
            background: var(--bg-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .admin-login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 50px 40px;
            max-width: 420px;
            width: 100%;
        }
        .admin-login-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .admin-login-card .logo h3 {
            font-weight: 800;
            color: var(--white);
        }
        .admin-login-card .logo h3 span {
            color: var(--cyan);
        }
        .admin-login-card .logo small {
            color: var(--muted);
            display: block;
        }
        .admin-login-card .form-control {
            background: var(--bg-input) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--white) !important;
            padding: 12px 16px;
            border-radius: var(--radius);
        }
        .admin-login-card .form-control:focus {
            border-color: var(--cyan) !important;
            box-shadow: 0 0 20px var(--cyan-glow) !important;
        }
        .admin-login-card .btn-login {
            background: var(--cyan);
            color: var(--bg-dark);
            font-weight: 600;
            padding: 14px;
            border-radius: 50px;
            border: none;
            width: 100%;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .admin-login-card .btn-login:hover {
            background: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 0 30px var(--cyan-glow);
        }
        .admin-login-card .form-label {
            color: var(--muted);
            font-weight: 500;
            font-size: 14px;
        }
        .alert {
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="admin-login-card">
        <div class="logo">
            <img src="<?php echo SITE_URL; ?>assets/images/logo-horizontal.png" alt="PunchyArts" height="45">
            <small>Administrator Login</small>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@example.com" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Login to Admin</button>
        </form>
        <p class="text-center mt-3">
            <a href="<?php echo SITE_URL; ?>" class="text-muted small">&larr; Back to Website</a>
        </p>
    </div>
</body>
</html>