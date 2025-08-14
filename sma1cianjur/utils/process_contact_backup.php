<?php
// Enhanced process_contact.php with security improvements
session_start();
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CSRF Protection
if(!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])){
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Rate limiting - simple implementation
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimitKey = 'contact_' . $ip;
if(!isset($_SESSION[$rateLimitKey])){
    $_SESSION[$rateLimitKey] = time();
} else {
    if(time() - $_SESSION[$rateLimitKey] < 60){ // 1 minute cooldown
        http_response_code(429);
        echo json_encode(['status' => 'error', 'message' => 'Mohon tunggu 1 menit sebelum mengirim pesan lagi']);
        exit;
    }
    $_SESSION[$rateLimitKey] = time();
}

// Input validation and sanitization
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$pesan = trim($_POST['pesan'] ?? '');

// Escape XSS
$nama = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$pesan = htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8');

if(!$nama || !$email || !$pesan){
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
    exit;
}

if(strlen($nama) > 100 || strlen($email) > 100 || strlen($pesan) > 1000){
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data terlalu panjang']);
    exit;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid']);
    exit;
}

// Enhanced database connection with PDO
try {
    require_once __DIR__ . '/db.php';
    
    $stmt = $pdo->prepare('INSERT INTO kontak (nama, email, pesan, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$nama, $email, $pesan, $_SERVER['REMOTE_ADDR']]);
    
    echo json_encode(['status' => 'success', 'message' => 'Terima kasih! Pesan Anda telah terkirim dan akan kami respon dalam 1-2 hari kerja.']);
    
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Maaf, terjadi kesalahan sistem. Silakan coba lagi nanti.']);
    exit;
}

// Optional: Send email notification using PHPMailer
$sendEmail = false; // Set to true when PHPMailer is configured
if($sendEmail){
    try {
        require __DIR__ . '/../vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // SMTP Configuration (update with real values)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // or your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-app-password';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('noreply@sma1cianjur.sch.id', 'SMA Negeri 1 Cianjur');
        $mail->addAddress('info@sma1cianjur.sch.id');
        $mail->Subject = 'Pesan Baru dari Website: ' . $nama;
        $mail->Body = "Pesan baru dari website:\n\nNama: $nama\nEmail: $email\nIP: {$_SERVER['REMOTE_ADDR']}\nWaktu: " . date('Y-m-d H:i:s') . "\n\nPesan:\n$pesan";
        
        $mail->send();
    } catch (Exception $e) {
        // Don't break the process if email fails
        error_log('Email error: ' . $e->getMessage());
    }
}
?>
