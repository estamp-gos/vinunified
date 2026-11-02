<?php
// /patchwebsite/includes/auth.php

// ============================================
// AUTHENTICATION FUNCTIONS
// ============================================

/**
 * Login user
 */
function loginUser($email, $password, $remember = false) {
    $db = getDB();
    
    // Get user by email
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return ['success' => false, 'message' => 'No account found with this email.'];
    }
    
    // Check password (plain text for now - will hash later)
    if ($password !== $user['password']) {
        // Log failed attempt
        logFailedAttempt($email);
        return ['success' => false, 'message' => 'Invalid password.'];
    }
    
    // Check if account is locked
    if (isAccountLocked($email)) {
        return ['success' => false, 'message' => 'Account is temporarily locked. Please try again later.'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_phone'] = $user['phone'] ?? '';
    $_SESSION['logged_in_at'] = time();
    
    // Remember me
    if ($remember) {
        setRememberToken($user['id']);
    }
    
    // Clear failed attempts
    clearFailedAttempts($email);
    
    // Update last login
    $stmt = $db->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    return ['success' => true, 'user' => $user];
}

/**
 * Register new user
 */
function registerUser($name, $email, $password, $phone = '') {
    $db = getDB();
    
    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'This email is already registered.'];
    }
    
    // Insert user
    $stmt = $db->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'customer')");
    if ($stmt->execute([$name, $email, $password, $phone])) {
        $userId = $db->lastInsertId();
        
        // Auto login
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = 'customer';
        $_SESSION['logged_in_at'] = time();
        
        return ['success' => true, 'user_id' => $userId];
    }
    
    return ['success' => false, 'message' => 'Registration failed. Please try again.'];
}

/**
 * Logout user
 */
function logoutUser() {
    // Clear remember me
    if (isset($_SESSION['user_id'])) {
        clearRememberToken($_SESSION['user_id']);
    }
    
    // Destroy session
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    return true;
}

/**
 * Get user by ID
 */
function getUserById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Update user profile
 */
function updateUserProfile($userId, $data) {
    $db = getDB();
    $fields = [];
    $params = [];
    
    $allowedFields = ['name', 'phone', 'profile_image'];
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $fields[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
    
    if (empty($fields)) {
        return ['success' => false, 'message' => 'No fields to update.'];
    }
    
    $params[] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    
    if ($stmt->execute($params)) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Update failed.'];
}

/**
 * Change password
 */
function changePassword($userId, $oldPassword, $newPassword) {
    $db = getDB();
    
    // Get current password
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user || $user['password'] !== $oldPassword) {
        return ['success' => false, 'message' => 'Current password is incorrect.'];
    }
    
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'message' => 'New password must be at least 6 characters.'];
    }
    
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    if ($stmt->execute([$newPassword, $userId])) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Password change failed.'];
}

// ============================================
// REMEMBER ME
// ============================================

function setRememberToken($userId) {
    $token = bin2hex(random_bytes(32));
    $expires = time() + 30 * 24 * 3600; // 30 days
    
    // Store in cookie
    setcookie('remember_token', $token, $expires, '/', '', false, true);
    
    // Store in database (you may need a remember_tokens table)
    $_SESSION['remember_token'] = $token;
}

function clearRememberToken($userId) {
    setcookie('remember_token', '', time() - 3600, '/');
    unset($_SESSION['remember_token']);
}

// ============================================
// LOGIN ATTEMPTS
// ============================================

function logFailedAttempt($email) {
    $ip = getClientIP();
    $attempts = $_SESSION['login_attempts'] ?? [];
    $attempts[$email] = [
        'count' => ($attempts[$email]['count'] ?? 0) + 1,
        'time' => time()
    ];
    $_SESSION['login_attempts'] = $attempts;
}

function isAccountLocked($email) {
    $attempts = $_SESSION['login_attempts'] ?? [];
    if (!isset($attempts[$email])) return false;
    
    if ($attempts[$email]['count'] >= MAX_LOGIN_ATTEMPTS) {
        if (time() - $attempts[$email]['time'] < LOCKOUT_TIME) {
            return true;
        } else {
            // Reset attempts
            unset($_SESSION['login_attempts'][$email]);
        }
    }
    return false;
}

function clearFailedAttempts($email) {
    unset($_SESSION['login_attempts'][$email]);
}

// ============================================
// PASSWORD RESET (Placeholder)
// ============================================

function generateResetToken($email) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['reset_token'][$email] = [
        'token' => $token,
        'expires' => time() + 3600 // 1 hour
    ];
    return $token;
}

function verifyResetToken($email, $token) {
    if (!isset($_SESSION['reset_token'][$email])) return false;
    $data = $_SESSION['reset_token'][$email];
    if ($data['token'] !== $token) return false;
    if (time() > $data['expires']) {
        unset($_SESSION['reset_token'][$email]);
        return false;
    }
    return true;
}

function resetPassword($email, $newPassword) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
    if ($stmt->execute([$newPassword, $email])) {
        unset($_SESSION['reset_token'][$email]);
        return true;
    }
    return false;
}
?>