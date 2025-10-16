<?php
// app/controller/Update/logout.php
require_once __DIR__ . '/autoload.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$uuid = $_SESSION['user_uuid'] ?? null;
$id = $_SESSION['user_id'] ?? null;

if ($id) prepared_query($conn, "UPDATE pengguna SET status_akun = 'Offline' WHERE id = ?", 'i', [$id]);
if ($uuid) prepared_query($conn, "INSERT INTO log_activity (pengguna_uuid, tipe_aktivitas, aktivitas_detail, ip_public) VALUES (?, ?, ?, ?)", 'ssss', [$uuid, 'logout', 'User logout', $_SERVER['REMOTE_ADDR'] ?? '']);

// clear session
session_unset();
session_destroy();
redirect('login');