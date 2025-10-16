<?php
    // system/functions.php
    // Fungsi umum: base_url, redirect, alert dan wrapper DB (opsional)

    function base_url($path = '') {
        global $base_url;
        return rtrim($base_url, '/') . '/' . ltrim($path, '/');
    }

    function redirect($url) {
        header("Location: " . base_url($url));
        exit;
    }

    function alert($msg, $type = 'info') {
        set_flash('swal', ['msg'=>$msg, 'type'=>$type]);
    }

    /**
     * db_get / db_exec
     * Simple wrapper untuk query biasa (jika tidak ingin prepared_query)
     * Tidak direkomendasikan untuk menggunakan ini dengan input user tanpa escaping.
     */
    function db_get($conn, $sql) {
        $res = mysqli_query($conn, $sql);
        if ($res === false) return false;
        $rows = [];
        while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
        return $rows;
    }
    function db_exec($conn, $sql) {
        return mysqli_query($conn, $sql);
    }

    function view($path) {
        $base = __DIR__ . '/../../public/view/';
        if (file_exists($base . $path)) {
            include $base . $path;
        } else {
            echo "<div style='color:red;'>View tidak ditemukan: {$path}</div>";
        }
    }

    // ================ UUID & Flash helpers =================
    function uuid_v4() {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    function set_flash($key, $value) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['flash'][$key] = $value;
    }
    function get_flash($key) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $v = $_SESSION['flash'][$key] ?? null;
        if ($v) unset($_SESSION['flash'][$key]);
        return $v;
    }

    // fungsi untuk mengecek apakah ada pengguna yang login atau tidak
    // jika pengguna sudah login, selalu tentang pengguna ke halaman dasboard jika mencoba mengakses halaman login melalui URL
    function redirect_if_logged_in($redirect_to = '/panel-dashboard') {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!empty($_SESSION['user_uuid']) && !empty($_SESSION['login']) && $_SESSION['login'] === true) {
            header("Location: " . base_url(ltrim($redirect_to, '/')));
            exit;
        }
    }
    