<?php
    // public/index.php
    // Router utama yang berada di public/ — hanya file ini yang diakses via web

    // Ini aktifkan hanya pada saat mode development(pengembangan), komentar pada saat di hosting
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Tandai bahwa include ini berjalan melalui index.php (lapisan proteksi di file lain)
    define('APP_SECURE', true);

    // autoload & helper ada di folder app/system
    require_once __DIR__ . '/../app/system/autoload.php';
    require_once __DIR__ . '/../app/config/config.php';

    // ========== AUTO MIGRATE (JALANKAN SEKALI SAAT DATABASE KOSONG) ==========
    if (defined('AUTO_MIGRATE') && AUTO_MIGRATE === true) {
        require_once __DIR__ . '/../app/system/migrate.php';
        $migrator = new MigrationManager($conn, __DIR__ . '/../app/system/migrations');

        // Hanya jalankan jika benar-benar butuh (cek ada table inti / jumlah migrasi)
        if ($migrator->needsMigrate()) {
            $results = $migrator->runPending();

            // _HANYA_ tampilkan ringkasan di mode development agar tidak mengganggu UX
            if (defined('APP_ENV') && APP_ENV === 'development') {
                echo "<div class='container mt-3'><div class='alert alert-info'>";
                echo "<strong>Migrasi otomatis dijalankan:</strong><pre>" . htmlspecialchars(print_r($results, true)) . "</pre>";
                echo "</div></div>";
            }
        }
    }

    // === Ambil URL ===
    $url = isset($_GET['url']) ? $_GET['url'] : '';
    $url = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));
    $page = $url[0] ?? '';

    // === Load Routes Modular ===
    $routes = require __DIR__ . '/../app/route/routes.php';

    // === Routing utama ===
    if (array_key_exists($page, $routes)) {
        $target = __DIR__ . '/../' . $routes[$page];
        if (file_exists($target)) {
            require_once $target;
        } else {
            http_response_code(500);
            include __DIR__ . '/error/500.php';
        }
    } else {
        http_response_code(404);
        include __DIR__ . '/error/404.php';
        exit;
    }
?>