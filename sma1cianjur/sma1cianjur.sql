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
