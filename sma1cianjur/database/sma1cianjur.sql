-- Database: sma1cianjur
CREATE DATABASE IF NOT EXISTS sma1cianjur CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sma1cianjur;

CREATE TABLE IF NOT EXISTS kegiatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(100) NOT NULL,
    deskripsi TEXT NOT NULL,
    tanggal DATE NOT NULL,
    gambar VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS berita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    konten TEXT NOT NULL,
    tanggal DATE NOT NULL,
    gambar VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS kontak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    tanggal_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dummy data kegiatan
INSERT INTO kegiatan (judul, deskripsi, tanggal, gambar) VALUES
('Lomba 17 Agustus', 'Serangkaian lomba tradisional untuk memperingati kemerdekaan Republik Indonesia.', '2025-08-17', 'assets/images/kegiatan1.jpg'),
('Pentas Seni Sekolah', 'Acara tahunan menampilkan bakat seni siswa dalam musik, tari, dan drama.', '2025-07-05', 'assets/images/kegiatan2.jpg'),
('Study Tour Bandung', 'Kunjungan edukatif ke museum dan industri kreatif di Bandung.', '2025-06-12', 'assets/images/kegiatan3.jpg');

-- Dummy data berita
INSERT INTO berita (judul, konten, tanggal, gambar) VALUES
('SMA 1 Cianjur Juara Olimpiade Matematika', 'Tim olimpiade matematika berhasil meraih juara 1 pada kompetisi regional.', '2025-05-20', 'assets/images/berita1.jpg'),
('Peresmian Gedung Baru', 'Peresmian gedung belajar baru yang dilengkapi fasilitas modern untuk mendukung proses belajar mengajar.', '2025-04-10', 'assets/images/berita2.jpg'),
('SMA 1 Cianjur Adakan Donor Darah', 'Kegiatan donor darah bekerja sama dengan PMI setempat, melibatkan siswa dan staf sekolah.', '2025-03-15', 'assets/images/berita3.jpg');

-- Enhanced security tables
-- Table for rate limiting (security feature)
CREATE TABLE IF NOT EXISTS rate_limit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    action VARCHAR(50) NOT NULL,
    attempts INT DEFAULT 1,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    blocked_until TIMESTAMP NULL,
    INDEX idx_ip_action (ip_address, action),
    INDEX idx_last_attempt (last_attempt)
);

-- Table for CSRF tokens (security feature)
CREATE TABLE IF NOT EXISTS csrf_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    INDEX idx_token (token),
    INDEX idx_ip_address (ip_address),
    INDEX idx_expires_at (expires_at)
);

-- Table for security logs
CREATE TABLE IF NOT EXISTS security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_address (ip_address),
    INDEX idx_action (action),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at)
);

-- Add new columns to existing kontak table for enhanced security
ALTER TABLE kontak 
ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) DEFAULT '127.0.0.1' AFTER pesan,
ADD COLUMN IF NOT EXISTS user_agent TEXT AFTER ip_address,
ADD COLUMN IF NOT EXISTS status ENUM('pending', 'reviewed', 'responded') DEFAULT 'pending' AFTER user_agent,
ADD INDEX IF NOT EXISTS idx_email (email),
ADD INDEX IF NOT EXISTS idx_ip_address (ip_address),
ADD INDEX IF NOT EXISTS idx_status (status);

-- Clean up procedures (run periodically)
-- DELETE FROM csrf_tokens WHERE expires_at < NOW();
-- DELETE FROM rate_limit WHERE last_attempt < DATE_SUB(NOW(), INTERVAL 1 DAY);
