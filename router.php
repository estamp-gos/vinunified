<?php
// router.php for PHP built-in development server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Never serve environment files over HTTP, even if one exists in the
// project root. Without this, the built-in server would hand out .env
// (including SMTP credentials) to anyone who requested it.
if (basename($uri) === '.env' || strpos(basename($uri), '.env.') === 0) {
    http_response_code(404);
    exit;
}
$file = __DIR__ . $uri;

// If existing static file (css, js, images, pdf), serve directly
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// If file.php exists (clean URLs like /thankyou or /legal)
if (file_exists($file . '.php')) {
    require_once $file . '.php';
    return true;
}

// Default index
if ($uri === '/' || $uri === '' || is_dir($file)) {
    require_once __DIR__ . '/index.php';
    return true;
}

return false;
