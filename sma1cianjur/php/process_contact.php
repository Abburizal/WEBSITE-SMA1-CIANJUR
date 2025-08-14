<?php
// process_contact.php - simpan ke DB dan kirim email (opsional)
header('Content-Type: text/plain; charset=utf-8');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$pesan = trim($_POST['pesan'] ?? '');

if(!$nama || !$email || !$pesan){
    http_response_code(400);
    echo "Semua field harus diisi.";
    exit;
}
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    http_response_code(400);
    echo "Email tidak valid.";
    exit;
}

// simpan ke database
require_once __DIR__ . '/db.php';
try {
    $stmt = $conn->prepare('INSERT INTO kontak (nama, email, pesan) VALUES (?, ?, ?)');
    $stmt->execute([$nama, $email, $pesan]);
} catch (Exception $e) {
    http_response_code(500);
    echo "Gagal menyimpan ke database: " . $e->getMessage();
    exit;
}

// kirim email (opsional) menggunakan PHPMailer jika tersedia
// Jika ingin aktifkan, letakkan folder PHPMailer di php/vendor/PHPMailer/src dan include autoload.php
$sendEmail = false; // ubah ke true kalau sudah menginstall PHPMailer
if($sendEmail){
    try {
        require __DIR__ . '/vendor/autoload.php'; // sesuaikan path
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        // konfigurasi SMTP contoh (ubah sesuai server)
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'user@example.com';
        $mail->Password = 'password';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('noreply@sma1cianjur.sch.id', 'SMA Negeri 1 Cianjur');
        $mail->addAddress('info@sma1cianjur.sch.id');
        $mail->Subject = 'Pesan dari situs web: ' . $nama;
        $mail->Body = "Nama: $nama\nEmail: $email\n\nPesan:\n$pesan";
        $mail->send();
    } catch (Exception $e) {
        // tidak fatal untuk pengguna
    }
}

echo "Terima kasih, pesan Anda telah terkirim.";
