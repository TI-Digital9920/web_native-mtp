<?php
return [
    'name' => '2025_10_10_000001_create_pengguna_table',
    'up' => function($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS pengguna (
            id INT AUTO_INCREMENT PRIMARY KEY,
            uuid CHAR(36) NOT NULL UNIQUE,
            username VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('pengelola','pengguna') DEFAULT 'pengguna',
            remember_token VARCHAR(255) NULL,
            remember_token_expired DATETIME NULL,
            status_akun ENUM('Online','Offline') DEFAULT 'Offline',
            login_terakhir DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
    },
    'down' => function($conn) {
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
        mysqli_query($conn, "DROP TABLE IF EXISTS pengguna");
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
    }
];