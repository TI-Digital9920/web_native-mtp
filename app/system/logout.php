<?php
require_once __DIR__ . '/autoload.php';

$uuid = $_SESSION['user_uuid'] ?? null;
$id   = $_SESSION['user_id'] ?? null;

// 1. Update status akun
if ($id) {
    prepared_query($conn,
        "UPDATE pengguna SET status_akun = 'Offline', login_terakhir = DATE_SUB(NOW(), INTERVAL 10 MINUTE) WHERE id = ?",
        'i',
        [$id]
    );
}

// 2. Hapus remember token
if ($uuid) {
    prepared_query(
        $conn,
        "UPDATE pengguna 
         SET remember_token = NULL, remember_token_expired = NULL 
         WHERE uuid = ?",
        's',
        [$uuid]
    );

    prepared_query(
        $conn,
        "INSERT INTO log_activity 
        (pengguna_uuid, tipe_aktivitas, aktivitas_detail, ip_public)
        VALUES (?, 'logout', 'User logout', ?)",
        'ss',
        [$uuid, $_SERVER['REMOTE_ADDR'] ?? '']
    );
}

// 3. Hapus cookie remember
if (isset($_COOKIE['remember'])) {
    setcookie('remember', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);
}

// 4. HAPUS SESSION (FINAL & AMAN)
$_SESSION = [];
session_unset();
session_destroy();
session_regenerate_id(true);

// 5. Redirect
redirect('auth');
exit;
