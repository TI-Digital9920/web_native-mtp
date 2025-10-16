<?php
/**
 * ============================================================
 * Security Headers Manager
 * ------------------------------------------------------------
 * Menambahkan header keamanan hanya di production environment.
 * Menampilkan notifikasi console di browser untuk debugging.
 * ============================================================
 */

// Pastikan header belum dikirim
if (headers_sent()) return;

// --- Deteksi environment ---
$is_local = false;

// 1️⃣ Cek dari APP_ENV (konstanta di config.php)
if (defined('APP_ENV') && strtolower(APP_ENV) !== 'production') {
    $is_local = true;
}

// 2️⃣ Cek dari .env jika tersedia
elseif (!empty($_ENV['APP_ENV']) && strtolower($_ENV['APP_ENV']) !== 'production') {
    $is_local = true;
}

// 3️⃣ Cek juga host lokal manual (fallback)
elseif (in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'])) {
    $is_local = true;
}

// --- Jika bukan local/dev maka aktifkan security headers ---
if (!$is_local) {
    // 🔒 Cegah klikjacking (iframe)
    header("X-Frame-Options: SAMEORIGIN");

    // 🚫 Cegah browser menebak tipe konten (MIME sniffing)
    header("X-Content-Type-Options: nosniff");

    // 🕵️ Batasi data referrer
    header("Referrer-Policy: strict-origin-when-cross-origin");

    // 🧱 Fallback anti-XSS (meski deprecated, tetap aman untuk browser lama)
    header("X-XSS-Protection: 1; mode=block");

    // 🛡️ Batasi akses API sensitif
    header("Permissions-Policy: geolocation=(), microphone=()");

    // 🌐 Batasi sumber konten hanya dari domain sendiri
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'");

    // ✅ Tambahkan HSTS hanya jika benar-benar HTTPS dan bukan localhost
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload");
    }

    // 🧩 Tambahkan notifikasi di browser console
    echo "<script>console.log('%c[SECURITY]', 'color: green; font-weight: bold;', 'Security headers aktif — mode: PRODUCTION');</script>";

} else {
    // 🚧 Mode development
    echo "<script>console.log('%c[SECURITY]', 'color: orange; font-weight: bold;', 'Security headers nonaktif — mode: DEVELOPMENT');</script>";
}