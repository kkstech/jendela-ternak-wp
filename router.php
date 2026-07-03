<?php
/**
 * router.php — PHP Built-in Server Router for WordPress
 *
 * PHP's built-in server does not support .htaccess or mod_rewrite.
 * This router script mimics the WordPress .htaccess rewrite rules,
 * allowing pretty permalinks to work without Apache.
 *
 * Usage: php -S 127.0.0.1:8000 router.php
 */

// Get the requested URI (without query string)
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the request is for an actual file or directory that exists, serve it directly
// (CSS, JS, images, PHP files, etc.)
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let PHP built-in server handle it directly
}

// Otherwise, route everything through WordPress's index.php
// This mimics the Apache .htaccess RewriteRule
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
require_once __DIR__ . '/index.php';
