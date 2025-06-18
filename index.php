<?php
/**
 * Shared Hosting Compatibility File
 * 
 * This file redirects all requests to the public/ directory
 * for shared hosting environments where .htaccess might not work
 */

// Get the request URI and clean it
$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . preg_replace('@/+$@', '', dirname($_SERVER['SCRIPT_NAME'])) . '/';
$uri = $_SERVER['REQUEST_URI'];
$segments = explode('/', trim($uri, '/'));
$requestPath = end($segments);

// If accessing root directory, redirect to public/
if ($requestPath === '/' || $requestPath === '' || $requestPath === '/index.php') {
    // Use HTTP redirect to public directory
    header('HTTP/1.1 302 Found');
    header('Location: ' . $base_url . 'public/');
    exit();
}

// For any other path, try to serve from public/
$publicFile = $base_url . 'public/' . $requestPath;

// If it's a PHP file in public/, execute it
if (is_file($publicFile) && pathinfo($publicFile, PATHINFO_EXTENSION) === 'php') {
    chdir($base_url . 'public/');
    require $publicFile;
    exit;
}

// If it's any other file in public/, serve it
if (is_file($publicFile)) {
    // Determine content type
    $extension = pathinfo($publicFile, PATHINFO_EXTENSION);
    $contentTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'svg' => 'image/svg+xml'
    ];
    
    if (isset($contentTypes[$extension])) {
        header('Content-Type: ' . $contentTypes[$extension]);
    }
    
    readfile($publicFile);
    exit;
}

// Default: Forward to Symfony front controller
header('HTTP/1.1 302 Found');
header('Location: ' . $base_url . 'public/');
exit();