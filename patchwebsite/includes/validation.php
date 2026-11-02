<?php
// /patchwebsite/includes/validation.php

// ============================================
// VALIDATION FUNCTIONS
// ============================================

/**
 * Validate required fields
 */
function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (empty($data[$field]) || trim($data[$field]) === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }
    return $errors;
}

/**
 * Validate email
 */
function validateEmailField($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'error' => 'Please enter a valid email address.'];
    }
    return ['valid' => true];
}

/**
 * Validate phone (USA)
 */
function validatePhoneField($phone) {
    if (!empty($phone) && !preg_match('/^[\+]?[0-9\s\-\(\)]{10,15}$/', $phone)) {
        return ['valid' => false, 'error' => 'Please enter a valid USA phone number (e.g., +1 800-123-4567).'];
    }
    return ['valid' => true];
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    if (strlen($password) < 6) {
        return ['valid' => false, 'error' => 'Password must be at least 6 characters.'];
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain at least one uppercase letter.'];
    }
    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain at least one lowercase letter.'];
    }
    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain at least one number.'];
    }
    return ['valid' => true];
}

/**
 * Validate order form data
 */
function validateOrder($data, $serviceType) {
    $errors = [];
    
    // Common required fields
    $required = ['service_type'];
    
    // Service-specific validation
    switch ($serviceType) {
        case 'digitizing':
            $required = array_merge($required, ['stitch_placement', 'size_width', 'size_height']);
            break;
        case 'vector':
            $required = array_merge($required, ['output_format']);
            break;
        case 'character':
            $required = array_merge($required, ['custom_type']);
            break;
        case 'patches':
            $required = array_merge($required, ['patch_size_width', 'patch_size_height', 'quantity']);
            if (isset($data['patch_type']) && $data['patch_type'] === 'physical') {
                $required[] = 'shipping_address';
            }
            break;
    }
    
    // Check required fields
    foreach ($required as $field) {
        if (empty($data[$field]) || trim($data[$field]) === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }
    
    // Validate numeric fields
    $numericFields = ['size_width', 'size_height', 'quantity'];
    foreach ($numericFields as $field) {
        if (isset($data[$field]) && !empty($data[$field])) {
            if (!is_numeric($data[$field]) || $data[$field] <= 0) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' must be a positive number.';
            }
        }
    }
    
    return $errors;
}

/**
 * Validate portfolio data
 */
function validatePortfolio($data) {
    $errors = [];
    
    if (empty($data['title']) || trim($data['title']) === '') {
        $errors[] = 'Title is required.';
    }
    if (empty($data['category']) || trim($data['category']) === '') {
        $errors[] = 'Category is required.';
    }
    
    return $errors;
}

/**
 * Validate blog data
 */
function validateBlog($data) {
    $errors = [];
    
    if (empty($data['title']) || trim($data['title']) === '') {
        $errors[] = 'Title is required.';
    }
    if (empty($data['content']) || trim($data['content']) === '') {
        $errors[] = 'Content is required.';
    }
    if (empty($data['slug']) || trim($data['slug']) === '') {
        $errors[] = 'Slug is required.';
    }
    
    return $errors;
}

/**
 * Validate FAQ data
 */
function validateFaq($data) {
    $errors = [];
    
    if (empty($data['question']) || trim($data['question']) === '') {
        $errors[] = 'Question is required.';
    }
    if (empty($data['answer']) || trim($data['answer']) === '') {
        $errors[] = 'Answer is required.';
    }
    
    return $errors;
}

/**
 * Validate pricing data
 */
function validatePricing($data) {
    $errors = [];
    
    if (empty($data['service_type']) || trim($data['service_type']) === '') {
        $errors[] = 'Service type is required.';
    }
    if (empty($data['title']) || trim($data['title']) === '') {
        $errors[] = 'Title is required.';
    }
    if (empty($data['price']) || trim($data['price']) === '') {
        $errors[] = 'Price is required.';
    }
    
    return $errors;
}

/**
 * Validate registration data
 */
function validateRegistration($data) {
    $errors = [];
    
    if (empty($data['name']) || trim($data['name']) === '') {
        $errors[] = 'Name is required.';
    }
    if (empty($data['email']) || trim($data['email']) === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($data['password'])) {
        $errors[] = 'Password is required.';
    } else {
        $passwordCheck = validatePassword($data['password']);
        if (!$passwordCheck['valid']) {
            $errors[] = $passwordCheck['error'];
        }
    }
    if (isset($data['confirm_password']) && $data['password'] !== $data['confirm_password']) {
        $errors[] = 'Passwords do not match.';
    }
    if (!empty($data['phone'])) {
        $phoneCheck = validatePhoneField($data['phone']);
        if (!$phoneCheck['valid']) {
            $errors[] = $phoneCheck['error'];
        }
    }
    
    return $errors;
}

/**
 * Validate contact form
 */
function validateContact($data) {
    $errors = [];
    
    if (empty($data['name']) || trim($data['name']) === '') {
        $errors[] = 'Name is required.';
    }
    if (empty($data['email']) || trim($data['email']) === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($data['subject']) || trim($data['subject']) === '') {
        $errors[] = 'Subject is required.';
    }
    if (empty($data['message']) || trim($data['message']) === '') {
        $errors[] = 'Message is required.';
    }
    
    return $errors;
}

/**
 * Display validation errors
 */
function displayErrors($errors) {
    if (empty($errors)) return '';
    
    $html = '<div class="alert alert-danger">';
    $html .= '<ul class="mb-0">';
    foreach ($errors as $error) {
        $html .= '<li>' . htmlspecialchars($error) . '</li>';
    }
    $html .= '</ul>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Get validation summary
 */
function validationSummary($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? null;
        
        // Required
        if (in_array('required', $rule) && (empty($value) || trim($value) === '')) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            continue;
        }
        
        // Email
        if (in_array('email', $rule) && !empty($value)) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' must be a valid email.';
            }
        }
        
        // Numeric
        if (in_array('numeric', $rule) && !empty($value)) {
            if (!is_numeric($value)) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number.';
            }
        }
        
        // Min length
        if (in_array('min', $rule) && !empty($value)) {
            $min = $rule[array_search('min', $rule) + 1] ?? 0;
            if (strlen($value) < $min) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' must be at least ' . $min . ' characters.';
            }
        }
        
        // Max length
        if (in_array('max', $rule) && !empty($value)) {
            $max = $rule[array_search('max', $rule) + 1] ?? 999;
            if (strlen($value) > $max) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' must not exceed ' . $max . ' characters.';
            }
        }
    }
    
    return $errors;
}
?>