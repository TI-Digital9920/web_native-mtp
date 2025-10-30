<?php

    // === coba baca .env jika tersedia ===
    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        require_once __DIR__ . '/../../vendor/autoload.php';
        if (file_exists(__DIR__ . '/../../.env')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
            $env_loaded = true;
        } else {
            $env_loaded = false;
        }
    } else {
        $env_loaded = false;
    }


    // === Konfigurasi dasar aplikasi ===
    // Ubah sesuai folder project
    $base_url = "http://localhost/myproject/";

    // === Opsi keamanan (opsional - aktifkan jika mau) ===
    $security = [
        'enable' => true,               // true = aktifkan helper security, false = non-aktif
        'csrf'   => true,               // CSRF token untuk form
        'escape_output' => true,        // escape output otomatis (HTML entities saat output)
        'prepared_statements' => true,  // pakai prepared statements (jika false, gunakan manual -> hati-hati)
        'encryption' => true,           // enkripsi data sensitif (openssl)
        'encryption_key' => 'change_this_to_a_long_random_key_32_chars_min' // penting ganti ini di key utama anda minimal 32 karakter

        // Cara mendapatkan keynya berikut :
        // 1. Buka terminal VSCode dengan cara tekan keyboard Ctrl + `
        // 2. masukkan perintah "php -r "echo bin2hex(random_bytes(32));" (Tanpa tanda kutip)
        // 3. Copy Key
        // 4. masukkan kedalam 'encryption_key' misalnya 'encryption_key' => '0a5a0267a695b85**********48d26d6c1322fd'
        // 5. key digunakan untuk sekali seumur hidup, jangan berikan key ini untuk orang lain
    ];

    // di bagian atas config.php (setelah koneksi db dibuat)
    define('APP_ENV', 'development'); // ganti ke 'production' di hosting nyata
    
    // optional: jika kamu mau men-disable auto-migrate di production
    define('AUTO_MIGRATE', true);

    // === Koneksi ke database (sesuaikan jika file .env tidak ada di root project)===
    if (!$env_loaded) {
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db   = "myproject";

        $conn = mysqli_connect($host, $user, $pass, $db);
        if (!$conn) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }
    } else {
        // Jika .env ditemukan, gunakan koneksi dari variabel lingkungan (tidak Perlu di ubah)
        $conn = mysqli_connect(
            $_ENV['DB_HOST'] ?? 'localhost',
            $_ENV['DB_USER'] ?? 'root',
            $_ENV['DB_PASS'] ?? '',
            $_ENV['DB_NAME'] ?? 'myproject'
        );
        if (!$conn) {
            die("Koneksi database gagal (ENV): " . mysqli_connect_error());
        }
    }

    // === Zona waktu lokal ===
    date_default_timezone_set('Asia/Makassar');
