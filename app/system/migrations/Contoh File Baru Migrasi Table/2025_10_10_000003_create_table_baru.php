<?php
return [
    'name' => '2025_10_10_000006_create_table_baru',
    'up' => function($conn) {
        // ===== TEMPLAT: aktifkan (hilangkan komentar) jika ingin membuat tabel ini =====
        /*
        $sql = "CREATE TABLE IF NOT EXISTS table_baru (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pengguna_uuid CHAR(36) NOT NULL,
            nama_lengkap VARCHAR(255) NOT NULL,
            tempat_lahir VARCHAR(255) NULL,
            tanggal_lahir DATE NULL,
            agama VARCHAR(50) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (pengguna_uuid) REFERENCES pengguna(uuid) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
        */
    },
    'down' => function($conn) {
        // Jika mengaktifkan up() di atas, uncomment baris berikut untuk drop
        /*
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
        mysqli_query($conn, "DROP TABLE IF EXISTS table_baru");
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
        */
    }
];