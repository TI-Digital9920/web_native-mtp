<?php
return [
    'name' => '2025_10_10_000004_create_log_activity_table',
    'up' => function($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS log_activity (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pengguna_uuid CHAR(36) NULL,
            tipe_aktivitas VARCHAR(100) NOT NULL,
            aktivitas_detail TEXT NULL,
            ip_public VARCHAR(45) NULL,
            waktu_tanggal_aktivitas DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (pengguna_uuid) REFERENCES pengguna(uuid) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
    },
    'down' => function($conn) {
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
        mysqli_query($conn, "DROP TABLE IF EXISTS log_activity");
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
    }
];