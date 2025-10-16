<?php
// app/system/migrate-runner.php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/migrate.php';

// buat instance migrator
$migrator = new MigrationManager($conn, __DIR__ . '/migrations');

// cek apakah ada migrasi yang belum dijalankan
if ($migrator->needsMigrate()) {
    echo "Menjalankan migrasi...\n";
    $results = $migrator->runPending();
    echo "Selesai. Hasil:\n";
    print_r($results);
} else {
    echo "Tidak ada migrasi baru. Database up-to-date.\n";
}