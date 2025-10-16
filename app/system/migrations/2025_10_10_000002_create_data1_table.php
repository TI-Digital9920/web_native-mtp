<?php
return [
    'name' => '2025_10_10_000007_create_data1_table',
    'up' => function($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS data1 (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pengguna_uuid CHAR(36) NOT NULL,
            judul VARCHAR(255) NOT NULL,
            deskripsi TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (pengguna_uuid) REFERENCES pengguna(uuid) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
    },
    'down' => function($conn) {
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
        mysqli_query($conn, "DROP TABLE IF EXISTS data1");
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
    }
];