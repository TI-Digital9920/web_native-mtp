<?php
    // app/system/autoload.php
    // Muat config, fungsi, dan security helper (semua di folder app/)
    session_start();

    require_once __DIR__ . '/../config/config.php';
    
    // Header Security (keamanan parsing data)
    require_once __DIR__ . '/security_headers.php';

    require_once __DIR__ . '/functions.php';
    require_once __DIR__ . '/security.php';

    // Autoload controller & view otomatis jika dipanggil class (tidak mempengaruhi include manual controller)
    spl_autoload_register(function($className) {
        $paths = [
            __DIR__ . '/../controller/' . $className . '.php',
            __DIR__ . '/../view/' . $className . '.php',
        ];
        foreach ($paths as $file) {
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    });

    // === Auto Sanitizer: Filter global untuk input ===
    if (isset($security['enable']) && $security['enable']) {
        // Recursive function untuk bersihkan input array
        function sanitize_array($arr) {
            $cleaned = [];
            foreach ($arr as $key => $val) {
                if (is_array($val)) {
                    $cleaned[$key] = sanitize_array($val);
                } else {
                    $cleaned[$key] = secure_input($val);
                }
            }
            return $cleaned;
        }

        // Auto sanitize global variables
        if (!empty($_POST))    $_POST    = sanitize_array($_POST);
        if (!empty($_GET))     $_GET     = sanitize_array($_GET);
        if (!empty($_REQUEST)) $_REQUEST = sanitize_array($_REQUEST);
    }