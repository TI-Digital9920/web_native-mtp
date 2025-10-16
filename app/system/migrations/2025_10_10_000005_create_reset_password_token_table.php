<?php
return [
    'name' => '2025_10_10_000005_create_reset_password_token_table',
    'up' => function($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS reset_password_token (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pengguna_uuid CHAR(36) NOT NULL,
            token_reset VARCHAR(255) NOT NULL,
            expired_token_reset DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (pengguna_uuid) REFERENCES pengguna(uuid) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!mysqli_query($conn, $sql)) throw new Exception(mysqli_error($conn));
    },
    'down' => function($conn) {
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
        mysqli_query($conn, "DROP TABLE IF EXISTS reset_password_token");
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
    }
];