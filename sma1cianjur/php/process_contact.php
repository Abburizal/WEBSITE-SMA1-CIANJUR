<?php
/**
 * Enhanced Contact Form Processing with Security
 * Handles form submission with CSRF protection, rate limiting, and XSS prevention
 */

// Security headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Start session and include dependencies
session_start();
require_once 'db.php';
require_once 'csrf_token.php';

try {
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Initialize security classes
    $csrfToken = new CSRFToken($pdo);
    $rateLimit = new RateLimit($pdo);
    $securityLogger = new SecurityLogger($pdo);
    
    // Get client IP address
    $ipAddress = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? 
                 $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
                 $_SERVER['HTTP_X_REAL_IP'] ?? 
                 $_SERVER['REMOTE_ADDR'] ?? 
                 '127.0.0.1';
    
    // Clean IP address (in case of comma-separated IPs)
    $ipAddress = trim(explode(',', $ipAddress)[0]);
    
    // Validate IP address
    if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
        $ipAddress = '127.0.0.1';
    }
    
    // Check rate limiting
    if ($rateLimit->isRateLimited($ipAddress)) {
        $securityLogger->log($ipAddress, 'rate_limit_exceeded', 'Contact form submission blocked', 'medium');
        http_response_code(429);
        echo json_encode([
            'success' => false, 
            'message' => 'Terlalu banyak percobaan. Silakan coba lagi nanti.'
        ]);
        exit;
    }
    
    // Record attempt
    $rateLimit->recordAttempt($ipAddress);
    
    // Validate CSRF token
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!$csrfToken->validateToken($submittedToken, $ipAddress)) {
        $securityLogger->log($ipAddress, 'csrf_token_invalid', 'Invalid CSRF token submitted', 'high');
        http_response_code(403);
        echo json_encode([
            'success' => false, 
            'message' => 'Token keamanan tidak valid. Silakan refresh halaman.'
        ]);
        exit;
    }
    
    // Get and sanitize input data
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Input validation
    $errors = [];
    
    // Validate name
    if (empty($nama)) {
        $errors[] = 'Nama harus diisi';
    } elseif (strlen($nama) < 2 || strlen($nama) > 100) {
        $errors[] = 'Nama harus antara 2-100 karakter';
    } elseif (!preg_match('/^[a-zA-Z\s\.]+$/', $nama)) {
        $errors[] = 'Nama hanya boleh mengandung huruf, spasi, dan titik';
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    } elseif (strlen($email) > 100) {
        $errors[] = 'Email terlalu panjang';
    }
    
    // Validate message
    if (empty($pesan)) {
        $errors[] = 'Pesan harus diisi';
    } elseif (strlen($pesan) < 10) {
        $errors[] = 'Pesan minimal 10 karakter';
    } elseif (strlen($pesan) > 1000) {
        $errors[] = 'Pesan maksimal 1000 karakter';
    }
    
    // Check for common spam patterns
    $spamPatterns = [
        '/\b(viagra|cialis|loan|casino|poker)\b/i',
        '/\b\d{4}[-\s]?\d{4}[-\s]?\d{4}[-\s]?\d{4}\b/', // Credit card pattern
        '/(http|https):\/\/[^\s]+/i' // URLs in message
    ];
    
    foreach ($spamPatterns as $pattern) {
        if (preg_match($pattern, $pesan)) {
            $securityLogger->log($ipAddress, 'spam_detected', "Spam pattern detected: $pattern", 'medium');
            $errors[] = 'Pesan mengandung konten yang tidak diizinkan';
            break;
        }
    }
    
    // If there are validation errors
    if (!empty($errors)) {
        echo json_encode([
            'success' => false, 
            'message' => implode('. ', $errors)
        ]);
        exit;
    }
    
    // Sanitize data for storage (prevent XSS)
    $nama = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $pesan = htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8');
    $userAgent = htmlspecialchars($userAgent, ENT_QUOTES, 'UTF-8');
    
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO kontak (nama, email, pesan, ip_address, user_agent, tanggal_kirim) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$nama, $email, $pesan, $ipAddress, $userAgent])) {
        // Log successful submission
        $securityLogger->log($ipAddress, 'contact_form_submitted', "Contact form submitted by $email", 'low');
        
        // Send email notification (if needed)
        // sendEmailNotification($nama, $email, $pesan);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Pesan berhasil dikirim! Kami akan membalas dalam 1-2 hari kerja.'
        ]);
    } else {
        $securityLogger->log($ipAddress, 'database_error', 'Failed to insert contact form data', 'high');
        echo json_encode([
            'success' => false, 
            'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Database error in contact form: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
    ]);
} catch (Exception $e) {
    error_log("General error in contact form: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Terjadi kesalahan. Silakan coba lagi.'
    ]);
}

/**
 * Email notification function (optional)
 */
function sendEmailNotification($nama, $email, $pesan) {
    // Implement email sending using PHPMailer if needed
    // This is a placeholder for future email functionality
    return true;
}
?>
