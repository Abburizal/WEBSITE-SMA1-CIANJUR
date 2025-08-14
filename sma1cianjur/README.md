# 🏫 SMA 1 CIANJUR WEBSITE

Website resmi SMA 1 Cianjur yang dibangun dengan HTML5, CSS3, JavaScript, PHP, dan MySQL.

## 📁 STRUKTUR PROJECT

```
sma1cianjur/
├── 📄 index.html              # Halaman utama
├── 📄 about.html              # Halaman tentang sekolah
├── 📄 activities.html         # Halaman kegiatan
├── 📄 news.html              # Halaman berita
├── 📄 contact.html           # Halaman kontak
├── 📄 composer.json          # Dependencies PHP
├── 📄 composer.lock          # Lock file dependencies
│
├── 📂 assets/                # Asset multimedia
│   └── images/               # Gambar dan foto
│       ├── berita1.jpg
│       ├── kegiatan1.jpg
│       ├── kegiatan2.jpg
│       ├── kegiatan3.jpg
│       └── sma-placeholder.jpg
│
├── 📂 css/                   # Stylesheet
│   └── style.css             # CSS utama dengan modern design
│
├── 📂 js/                    # JavaScript files
│   └── script.js             # JavaScript utama dengan security features
│
├── 📂 php/                   # PHP backend files
│   └── process_contact.php   # Pemrosesan form kontak dengan security
│
├── 📂 config/                # Konfigurasi sistem
│   └── db.php                # Konfigurasi database PDO
│
├── 📂 includes/              # Include files dan classes
│   └── csrf_token.php        # Security classes (CSRF, Rate Limiting, Logger)
│
├── 📂 database/              # Database files
│   └── sma1cianjur.sql       # Database schema dan data
│
├── 📂 utils/                 # Utilitas dan tools
│   ├── test_db.php           # Testing database connection
│   ├── generate_favicon.html # Generator favicon
│   └── process_contact_backup.php # Backup file
│
├── 📂 docs/                  # Dokumentasi
│   └── SECURITY_AUDIT_REPORT.md # Laporan audit keamanan
│
└── 📂 vendor/                # Composer dependencies
    └── phpmailer/            # PHPMailer untuk email
```

## 🚀 INSTALASI

### 1. **Requirements**
- PHP 8.0+
- MySQL 5.7+
- Web Server (Apache/Nginx)
- Composer

### 2. **Setup Database**
```sql
-- Import database schema
mysql -u username -p sma1cianjur < database/sma1cianjur.sql
```

### 3. **Konfigurasi Database**
Edit file `config/db.php`:
```php
$host = "localhost";
$dbname = "sma1cianjur";
$username = "your_username";
$password = "your_password";
```

### 4. **Install Dependencies**
```bash
composer install
```

### 5. **Test Installation**
```bash
php utils/test_db.php
```

## 🔒 FITUR KEAMANAN

### ✅ **Implemented Security Features**
- **CSRF Protection**: Token-based form protection
- **XSS Prevention**: Input sanitization dengan htmlspecialchars()
- **SQL Injection Protection**: PDO prepared statements
- **Rate Limiting**: Max 5 submissions per 5 minutes per IP
- **Spam Detection**: Pattern matching untuk konten spam
- **Security Headers**: X-Frame-Options, X-XSS-Protection
- **Input Validation**: Strict validation untuk semua input
- **Security Logging**: Comprehensive security event logging

### 🛡️ **Security Level**: ⭐⭐⭐⭐⭐ (Enterprise Grade)

## 📱 FITUR WEBSITE

### 🎨 **Modern UI/UX**
- Responsive design dengan Bootstrap 5.3.2
- Google Fonts (Inter) untuk typography profesional
- CSS Variables untuk konsistensi design
- Smooth animations dan transitions
- Dark mode compatible colors
- Accessibility features (ARIA labels, semantic HTML)

### 🔧 **Fungsionalitas**
- **Contact Form**: Form kontak dengan validasi dan security
- **Activity Gallery**: Showcase kegiatan sekolah
- **News Section**: Berita dan pengumuman
- **School Information**: Informasi lengkap sekolah
- **Responsive Navigation**: Menu navigasi mobile-friendly

### 📊 **Backend Features**
- **Database Integration**: MySQL dengan PDO
- **Email Integration**: PHPMailer support
- **Security Monitoring**: Real-time security logging
- **Rate Limiting**: Automatic spam prevention
- **Error Handling**: Comprehensive error management

## 🌐 DEPLOYMENT

### **Production Checklist**
- [ ] Update database credentials di `config/db.php`
- [ ] Enable HTTPS/SSL certificate
- [ ] Set proper file permissions (644 for files, 755 for dirs)
- [ ] Configure server security headers
- [ ] Set up regular database backups
- [ ] Monitor `security_logs` table
- [ ] Configure fail2ban for IP blocking
- [ ] Test all forms and functionalities

## 📞 SUPPORT

Untuk support teknis, silakan hubungi:
- **Email**: admin@sma1cianjur.sch.id
- **Repository**: [GitHub](https://github.com/Abburizal/WEBSITE-SMA1-CIANJUR)

## 📝 LICENSE

Copyright © 2025 SMA 1 Cianjur. All rights reserved.

---
**Built with ❤️ for SMA 1 Cianjur**
