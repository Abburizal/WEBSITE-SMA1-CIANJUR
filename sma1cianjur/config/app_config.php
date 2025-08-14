<?php
/**
 * Application Configuration
 * Central configuration for the SMA 1 Cianjur website
 */

// Application settings
define('APP_NAME', 'SMA 1 Cianjur');
define('APP_VERSION', '2.0.0');
define('APP_URL', 'https://sma1cianjur.sch.id');

// Security settings
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour
define('RATE_LIMIT_ATTEMPTS', 5);
define('RATE_LIMIT_WINDOW', 300); // 5 minutes
define('RATE_LIMIT_BLOCK_DURATION', 900); // 15 minutes

// Email settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', ''); // Set your email
define('SMTP_PASSWORD', ''); // Set your password
define('FROM_EMAIL', 'admin@sma1cianjur.sch.id');
define('FROM_NAME', 'SMA 1 Cianjur');

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Logging settings
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_RETENTION_DAYS', 30);

// Development settings
define('DEVELOPMENT_MODE', false); // Set to false in production
define('DEBUG_MODE', false); // Set to false in production

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

// Error reporting
if (DEVELOPMENT_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Timezone
date_default_timezone_set('Asia/Jakarta');
?>
