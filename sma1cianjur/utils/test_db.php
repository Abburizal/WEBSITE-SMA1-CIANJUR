<?php
// Test database connection
try {
    include __DIR__ . '/../config/db.php';
    $pdo = getDatabaseConnection();
    echo "Database connection successful!" . PHP_EOL;
    
    // Test if security tables exist
    $tables = ['csrf_tokens', 'rate_limit', 'security_logs'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "Table '$table' exists." . PHP_EOL;
        } else {
            echo "Table '$table' does not exist - please run the SQL update." . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . PHP_EOL;
}
?>
