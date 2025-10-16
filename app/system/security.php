<?php
    // system/security.php
    // Helper keamanan: XSS sanitization, CSRF, password hashing, prepared statements, encryption
    // Semua fungsi memeriksa konfigurasi di config.php

    if (!isset($security)) {
        trigger_error("Security helper: konfigurasi \$security tidak ditemukan. Pastikan require config.php terlebih dahulu.", E_USER_WARNING);
    }

    /**
     * secure_input
     * Sanitasi input user (trim + strip_tags optional + htmlspecialchars)
     * Gunakan sebelum menyimpan atau memproses input.
     */
    function secure_input($value, $allow_html = false) {
        global $security;
        if (is_array($value)) {
            return array_map(fn($v) => secure_input($v, $allow_html), $value);
        }
        $v = trim($value);
        // jangan strip_tags secara agresif jika butuh HTML (opsional), tapi default aman
        if (!$allow_html) {
            $v = strip_tags($v);
        } else {
            // Optional: whitelist tag aman
            $v = strip_tags($v, '<p><b><i><u><br><strong><em>');
        }
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * escape_output
     * Gunakan saat menampilkan data ke HTML agar ter-escape jika opsi aktif.
     */
    function escape_output($value) {
        global $security;
        if (empty($security['escape_output'])) return $value;
        if (is_array($value)) {
            return array_map('escape_output', $value);
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * password helpers
     */
    function encrypt_password($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    function verify_password($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * CSRF token helpers
     */
    function csrf_token() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['_csrf_token']) || time() > ($_SESSION['_csrf_expire'] ?? 0)) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['_csrf_expire'] = time() + 7200; // 2 jam
        }
        return $_SESSION['_csrf_token'];
    }
    function csrf_field() {
        return '<input type="hidden" name="_csrf_token" value="'.csrf_token().'">';
    }
    function verify_csrf($token) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $valid = isset($_SESSION['_csrf_token']) 
        && hash_equals($_SESSION['_csrf_token'], (string)$token)
        && time() <= ($_SESSION['_csrf_expire'] ?? 0);
        if ($valid) unset($_SESSION['_csrf_token']); // opsional: single-use
        return $valid;
    }

    /**
     * prepared_query
     */
    function prepared_query($conn, $sql, $types = '', $params = []) {
        // Jika server tidak mendukung mysqlnd, fallback ke versi lama
        if (!function_exists('mysqli_stmt_get_result')) {
            error_log('mysqlnd tidak tersedia, fallback ke mode manual.');
            // === Panggil versi lama kamu di sini ===
            return legacy_prepared_query($conn, $sql, $types, $params);
        }

        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            error_log('Prepare gagal: '.mysqli_error($conn));
            return false;
        }

        if (!empty($types) && !empty($params)) {
            $bind_names = [$types];
            foreach ($params as $i => $val) {
                $bind_names[] = &$params[$i];
            }
            call_user_func_array([$stmt, 'bind_param'], $bind_names);
        }

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Execute gagal: '.mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }

        $sql_type = strtoupper(strtok($sql, " "));
        switch ($sql_type) {
            case 'SELECT':
                $result = mysqli_stmt_get_result($stmt);
                $data = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
                mysqli_stmt_close($stmt);
                return $data;
            case 'INSERT':
                $insert_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);
                return $insert_id;
            default:
                $affected = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);
                return $affected >= 0;
        }
    }

    function legacy_prepared_query($conn, $sql, $types = '', $params = []) {
        // Paste isi fungsi lama kamu di sini (versi dengan fallback manual)
        global $security;
        if (empty($security['prepared_statements']) || !function_exists('mysqli_prepare')) {
            $msg = "Prepared statements non-aktif. Anda menggunakan fallback (TIDAK DIANJURKAN).";
            error_log($msg);
            foreach ($params as $p) {
                $p_escaped = mysqli_real_escape_string($conn, (string)$p);
                $sql = preg_replace('/\?/', "'$p_escaped'", $sql, 1);
            }
            return mysqli_query($conn, $sql);
        }

        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            error_log('Prepare gagal: '.mysqli_error($conn));
            return false;
        }
        if (!empty($types) && !empty($params)) {
            $bind_names[] = $types;
            for ($i = 0; $i < count($params); $i++) {
                $bind_name = 'bind' . $i;
                $$bind_name = $params[$i];
                $bind_names[] = &$$bind_name;
            }
            call_user_func_array([$stmt, 'bind_param'], $bind_names);
        }

        $exec = mysqli_stmt_execute($stmt);
        if (!$exec) {
            error_log('Execute gagal: '.mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }

        $meta = mysqli_stmt_result_metadata($stmt);
        if ($meta) {
            $row = [];
            $fields = [];
            $results = [];
            while ($field = mysqli_fetch_field($meta)) {
                $fields[] = &$row[$field->name];
            }
            call_user_func_array([$stmt, 'bind_result'], $fields);
            while (mysqli_stmt_fetch($stmt)) {
                $r = [];
                foreach ($row as $k => $v) $r[$k] = $v;
                $results[] = $r;
            }
            mysqli_free_result($meta);
            mysqli_stmt_close($stmt);
            return $results;
        } else {
            $affected = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            return $affected >= 0;
        }
    }


    /**
     * Encryption helpers
     */
    function encrypt_data($plaintext) {
        global $security;
        if (empty($security['encryption']) || empty($security['encryption_key'])) return $plaintext;
        $method = 'AES-256-CBC';
        $key = hash('sha256', $security['encryption_key'], true);
        $iv = random_bytes(openssl_cipher_iv_length($method));
        $salt = random_bytes(16);
        $cipher = openssl_encrypt($salt . $plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $iv . $cipher, $key, true);
        return base64_encode($hmac . $iv . $cipher);
    }
    function decrypt_data($ciphertext) {
        global $security;
        if (empty($security['encryption']) || empty($security['encryption_key'])) return $ciphertext;
        $method = 'AES-256-CBC';
        $key = hash('sha256', $security['encryption_key'], true);
        $data = base64_decode($ciphertext);
        $hmac = substr($data, 0, 32);
        $ivlen = openssl_cipher_iv_length($method);
        $iv = substr($data, 32, $ivlen);
        $cipher = substr($data, 32 + $ivlen);
        $calc = hash_hmac('sha256', $iv . $cipher, $key, true);
        if (!hash_equals($hmac, $calc)) return false; // data rusak / tampered
        $plaintext = openssl_decrypt($cipher, $method, $key, OPENSSL_RAW_DATA, $iv);
        return substr($plaintext, 16); // hapus salt
    }