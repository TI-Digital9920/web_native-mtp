<?php
// controller/Insert/pengguna.php
require_once __DIR__ . '/../../system/autoload.php';

// jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // cek CSRF jika aktif
    if (!empty($security['csrf']) && !$security['csrf']) {
        // CSRF dimatikan di config (tidak direkomendasikan)
    }

    if (!empty($security['csrf']) && !verify_csrf($_POST['_csrf_token'] ?? '')) {
        alert('Token CSRF tidak valid.', 'danger');
        return; // hentikan proses
    }

    // ambil input
    $username = secure_input($_POST['username'] ?? '');
    $email = secure_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // validasi sederhana
    if (empty($username) || empty($email) || empty($password)) {
        alert('Semua field wajib diisi.', 'warning');
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        alert('Format email tidak valid', 'warning');
        return;
    }

    // cek duplikat
    $check = prepared_query($conn, "SELECT id FROM pengguna WHERE email = ? LIMIT 1", 's', [$email]);
    if ($check && count($check) > 0) {
        alert('Email sudah terdaftar', 'warning');
        return;
    }

    $user_uuid = uuid_v4();
    $hash = encrypt_password($password);

    // simpan ke DB
    $sql = "INSERT INTO pengguna (uuid, username, email, password, role, status_akun) VALUES (?, ?, ?, ?, ?, ? )";
    $res = prepared_query($conn, $sql, 'ssssss', [$user_uuid, $username, $email, $hash, 'pengguna', 'Offline']);

    if ($res) {
        alert('Registrasi berhasil. Silakan masuk.', 'success');
        redirect('login');
    } else {
        alert('Gagal menyimpan data. Silakan coba lagi atau lihat error log.', 'danger');
    }
}