<?php
// Enhanced database connection with PDO and security improvements
$host = "localhost";
$dbname = "sma1cianjur";
$username = "root";
$password = "";

try {
    // Use PDO for better security and features
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Don't echo connection success for security
    // Only log connection errors
    
} catch (PDOException $e) {
    // Log error securely, don't expose database details
    error_log('Database connection failed: ' . $e->getMessage());
    
    // Show generic error to user
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        die('Sistem sedang dalam perbaikan. Silakan coba lagi nanti.');
    }
}

// Keep backward compatibility with mysqli for existing code
$conn = null;
try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception('Connection failed');
    }
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log('MySQLi connection failed: ' . $e->getMessage());
    $conn = null;
}

// Function to get PDO connection
function getDatabaseConnection() {
    global $pdo;
    return $pdo;
}
?>
