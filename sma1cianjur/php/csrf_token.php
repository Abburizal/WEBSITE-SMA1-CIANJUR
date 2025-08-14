<?php
/**
 * CSRF Token Management
 * Provides secure token generation and validation
 */

class CSRFToken {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Generate a new CSRF token
     */
    public function generateToken($ipAddress) {
        // Clean up expired tokens
        $this->cleanupExpiredTokens();
        
        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO csrf_tokens (token, ip_address, expires_at) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$token, $ipAddress, $expiresAt]);
            
            return $token;
        } catch (PDOException $e) {
            error_log("CSRF Token generation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate CSRF token
     */
    public function validateToken($token, $ipAddress) {
        if (empty($token)) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT id FROM csrf_tokens 
                WHERE token = ? AND ip_address = ? AND expires_at > NOW() AND used = FALSE
            ");
            $stmt->execute([$token, $ipAddress]);
            
            if ($stmt->fetch()) {
                // Mark token as used
                $updateStmt = $this->pdo->prepare("
                    UPDATE csrf_tokens SET used = TRUE WHERE token = ?
                ");
                $updateStmt->execute([$token]);
                
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("CSRF Token validation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clean up expired tokens
     */
    private function cleanupExpiredTokens() {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM csrf_tokens 
                WHERE expires_at < NOW() OR (used = TRUE AND created_at < DATE_SUB(NOW(), INTERVAL 1 DAY))
            ");
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("CSRF Token cleanup error: " . $e->getMessage());
        }
    }
}

/**
 * Rate Limiting Class
 */
class RateLimit {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Check if IP is rate limited
     */
    public function isRateLimited($ipAddress, $action = 'contact_form', $maxAttempts = 5, $timeWindow = 300) {
        try {
            // Clean up old entries
            $this->cleanupOldEntries($timeWindow);
            
            // Check current attempts
            $stmt = $this->pdo->prepare("
                SELECT attempts, blocked_until FROM rate_limit 
                WHERE ip_address = ? AND action = ? AND last_attempt > DATE_SUB(NOW(), INTERVAL ? SECOND)
            ");
            $stmt->execute([$ipAddress, $action, $timeWindow]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Check if currently blocked
                if ($result['blocked_until'] && strtotime($result['blocked_until']) > time()) {
                    return true;
                }
                
                // Check attempts
                if ($result['attempts'] >= $maxAttempts) {
                    // Block for 15 minutes
                    $blockedUntil = date('Y-m-d H:i:s', time() + 900);
                    $updateStmt = $this->pdo->prepare("
                        UPDATE rate_limit 
                        SET blocked_until = ?, last_attempt = NOW()
                        WHERE ip_address = ? AND action = ?
                    ");
                    $updateStmt->execute([$blockedUntil, $ipAddress, $action]);
                    
                    return true;
                }
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Rate limit check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Record attempt
     */
    public function recordAttempt($ipAddress, $action = 'contact_form') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO rate_limit (ip_address, action, attempts, last_attempt) 
                VALUES (?, ?, 1, NOW())
                ON DUPLICATE KEY UPDATE 
                    attempts = attempts + 1,
                    last_attempt = NOW()
            ");
            $stmt->execute([$ipAddress, $action]);
        } catch (PDOException $e) {
            error_log("Rate limit record error: " . $e->getMessage());
        }
    }
    
    /**
     * Clean up old entries
     */
    private function cleanupOldEntries($timeWindow) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM rate_limit 
                WHERE last_attempt < DATE_SUB(NOW(), INTERVAL ? SECOND)
                AND (blocked_until IS NULL OR blocked_until < NOW())
            ");
            $stmt->execute([$timeWindow * 2]); // Keep entries for twice the time window
        } catch (PDOException $e) {
            error_log("Rate limit cleanup error: " . $e->getMessage());
        }
    }
}

/**
 * Security Logger
 */
class SecurityLogger {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Log security event
     */
    public function log($ipAddress, $action, $details = '', $severity = 'low') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO security_logs (ip_address, action, details, severity) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$ipAddress, $action, $details, $severity]);
        } catch (PDOException $e) {
            error_log("Security log error: " . $e->getMessage());
        }
    }
    
    /**
     * Clean up old logs (keep last 30 days)
     */
    public function cleanup() {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM security_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Security log cleanup error: " . $e->getMessage());
        }
    }
}
?>
