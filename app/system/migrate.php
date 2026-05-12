<?php
// app/system/migrate.php
// Migration manager ringan untuk PHP native

class MigrationManager {
    protected $conn;
    protected $dir;

    public function __construct($conn, $migration_dir) {
        if (!$conn || mysqli_connect_errno()) {
            die("<div style='color:red;'>Koneksi database gagal: " . mysqli_connect_error() . "</div>");
        }
        $this->conn = $conn;
        $this->dir  = rtrim($migration_dir, '/\\') . DIRECTORY_SEPARATOR;
    }

    private function log($message, $type = 'info') {
        $colors = [
            'info' => '#17a2b8',
            'success' => '#28a745',
            'warning' => '#ffc107',
            'error' => '#dc3545'
        ];
        $color = $colors[$type] ?? '#999';
        echo "<div style='font-family: monospace; color: {$color}; margin-left:10px;'>• {$message}</div>";
    }

    public function ensureMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        if (!mysqli_query($this->conn, $sql)) {
            throw new Exception('Gagal membuat tabel migrations: ' . mysqli_error($this->conn));
        }
    }

    private function tableExists($table) {
        $t = mysqli_real_escape_string($this->conn, $table);
        $sql = "SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '$t'";
        $res = mysqli_query($this->conn, $sql);
        $r = mysqli_fetch_assoc($res);
        return ((int)$r['cnt']) > 0;
    }

    public function getExecuted() {
        $this->ensureMigrationsTable();
        $res = mysqli_query($this->conn, "SELECT migration FROM migrations");
        $done = [];
        while ($row = mysqli_fetch_assoc($res)) $done[] = $row['migration'];
        return $done;
    }

    public function getMigrationFiles() {
        $files = glob($this->dir . '*.php');
        natcasesort($files);
        $list = [];
        foreach ($files as $f) {
            $m = include $f;
            $name = $m['name'] ?? pathinfo($f, PATHINFO_FILENAME);
            $list[$name] = ['file' => $f, 'migration' => $m];
        }
        return $list;
    }

    public function needsMigrate() {
        // === Ambil semua file migrasi yang tersedia ===
        $files = $this->getMigrationFiles();
        if (count($files) === 0) return false; // tidak ada file migrasi sama sekali

        // pastikan tabel migrations sudah ada
        $this->ensureMigrationsTable();

        // Cek apakah ada tabel dari setiap migrasi yang belum dibuat
        foreach ($files as $name => $info) {
            $migration = $info['migration'];
            if (!isset($migration['up'])) continue;

            // Ambil isi fungsi up() untuk mendeteksi nama tabel yang dibuat
            $content = '';
            ob_start();
            try {
                $func = new ReflectionFunction($migration['up']);
                $file  = file($func->getFileName());
                $start = $func->getStartLine();
                $end   = $func->getEndLine();
                $content = implode("", array_slice($file, $start-1, $end - $start + 1));
            } catch (Throwable $t) {}
            ob_end_clean();

            // Cari nama tabel dari perintah CREATE TABLE
            if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $content, $m)) {
                $table = $m[1];

                // Jika tabel belum ada, berarti migrasi dibutuhkan
                if (!$this->tableExists($table)) {
                    // tabel hilang → butuh migrasi ulang
                    return true;
                }
                
            }
        }

        // === Jika sampai di sini, berarti semua tabel ada ===
        // Sekarang cek apakah jumlah migrasi di DB sudah sama dengan jumlah file migrasi
        $res = mysqli_query($this->conn, "SELECT COUNT(*) AS cnt FROM migrations");
        $r = mysqli_fetch_assoc($res);
        $db_count = (int)$r['cnt'];
        $file_count = count($files);

        // Jika sudah sama, berarti tidak perlu migrasi ulang
        if ($db_count >= $file_count) {
            return false; // ✅ ini bagian penting untuk menghentikan alert migrasi
        }

        // Jika jumlah file migrasi lebih banyak dari yang tercatat di DB, berarti ada migrasi baru
        return true;

        // return ((int)$r['cnt']) < count($files);
    }

    public function runPending() {
        $this->ensureMigrationsTable();
        $done = $this->getExecuted();
        $files = $this->getMigrationFiles();
        $results = [];

        foreach ($files as $name => $info) {
            $migration = $info['migration'];

            // Cari nama tabel dari fungsi up()
            $content = '';
            ob_start();
            try {
                $func = new ReflectionFunction($migration['up']);
                $file  = file($func->getFileName());
                $start = $func->getStartLine();
                $end   = $func->getEndLine();
                $content = implode("", array_slice($file, $start-1, $end - $start + 1));
            } catch (Throwable $t) {}
            ob_end_clean();

            $table = null;
            if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $content, $m)) {
                $table = $m[1];
            }

            $tableMissing = $table && !$this->tableExists($table);
            $alreadyDone  = in_array($name, $done);

            // Jalankan migrasi jika:
            // - Belum tercatat di migrations, atau
            // - Tabelnya hilang (terhapus manual)
            if ($alreadyDone && !$tableMissing) {
                $results[$name] = ['status' => 'skipped'];
                continue;
            }

            try {
                if (!isset($migration['up']) || !is_callable($migration['up'])) {
                    throw new Exception("Migration '{$name}' tidak punya method up.");
                }

                // Jalankan migrasi up()
                call_user_func($migration['up'], $this->conn);

                // Jika belum tercatat, masukkan ke tabel migrations
                if (!$alreadyDone) {
                    $stmt = mysqli_prepare($this->conn, "INSERT INTO migrations (migration) VALUES (?)");
                    mysqli_stmt_bind_param($stmt, 's', $name);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                $results[$name] = [
                    'status' => $tableMissing ? 'recreated' : 'ok'
                ];
            } catch (Exception $e) {
                $results[$name] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                return $results; // hentikan jika error
            }
        }

        return $results;
    }

    public function rollbackLast() {
        $this->ensureMigrationsTable();
        $res = mysqli_query($this->conn, "SELECT id, migration FROM migrations ORDER BY id DESC LIMIT 1");
        if (!$res || mysqli_num_rows($res) === 0) return ['status' => 'none'];
        $row = mysqli_fetch_assoc($res);
        $name = $row['migration'];
        $files = $this->getMigrationFiles();
        if (!isset($files[$name])) return ['status' => 'file_not_found'];
        $down = $files[$name]['migration']['down'] ?? null;
        try {
            if ($down && is_callable($down)) {
                call_user_func($down, $this->conn);
            } else {
                // fallback: drop table jika nama migration mengikuti pola create_xxx_table
                // (tidak selalu aman, tapi fallback)
                // nothing by default
            }
            mysqli_query($this->conn, "DELETE FROM migrations WHERE migration = '".mysqli_real_escape_string($this->conn, $name)."' LIMIT 1");
            return ['status' => 'ok', 'migration' => $name];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function columnExists($table, $column) {
        $t = mysqli_real_escape_string($this->conn, $table);
        $c = mysqli_real_escape_string($this->conn, $column);
        $sql = "SELECT COUNT(*) AS cnt 
                FROM information_schema.columns 
                WHERE table_schema = DATABASE() AND table_name = '$t' AND column_name = '$c'";
        $res = mysqli_query($this->conn, $sql);
        $r = mysqli_fetch_assoc($res);
        return ((int)$r['cnt']) > 0;
    }

    public function getColumnType($table, $column) {
        $t = mysqli_real_escape_string($this->conn, $table);
        $c = mysqli_real_escape_string($this->conn, $column);
        $sql = "SELECT COLUMN_TYPE FROM information_schema.columns 
                WHERE table_schema = DATABASE() AND table_name = '$t' AND column_name = '$c'";
        $res = mysqli_query($this->conn, $sql);
        $r = mysqli_fetch_assoc($res);
        return $r ? $r['COLUMN_TYPE'] : null;
    }

    private function escape($s) {
        return str_replace('`', '``', $s);
    }

    public function rollbackAll() {
        $this->ensureMigrationsTable();
        $res = mysqli_query($this->conn, "SELECT migration FROM migrations ORDER BY id DESC");
        $rolled = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $m = $row['migration'];
            $files = $this->getMigrationFiles();
            if (isset($files[$m]['migration']['down'])) {
                call_user_func($files[$m]['migration']['down'], $this->conn);
                mysqli_query($this->conn, "DELETE FROM migrations WHERE migration = '".mysqli_real_escape_string($this->conn, $m)."'");
                $rolled[] = $m;
            }
        }
        return $rolled;
    }

}