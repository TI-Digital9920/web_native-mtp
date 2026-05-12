<?php
// app/system/Installer.php
namespace App;

class Installer {
    public static function postInstall() {
        $root = dirname(__DIR__, 2); // root project

        // create .env from example jika belum ada
        if (!file_exists($root . '/.env') && file_exists($root . '/.env.example')) {
            copy($root . '/.env.example', $root . '/.env');
            echo ".env created from .env.example\n";
        }

        // create storage/logs jika belum ada
        @mkdir($root . '/app/storage/logs', 0755, true);

        echo "Installer finished. Please edit .env with your DB credentials if needed.\n";
    }
}