<?php
    // Extensi Intelephense berhenti warning
    if (!defined('RAW_JSON_REQUEST')) {
        define('RAW_JSON_REQUEST', false);
    }
        
    // Wajib Include Di setiap Halamn UI " define('RAW_JSON_REQUEST', true); " tanpa tanda kutip dua
    
    // app/system/autoload.php
    // Muat config, fungsi, dan security helper (semua di folder app/)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    require_once __DIR__ . '/../config/config.php';

    // === Pengaturan Error Berdasarkan Environment ===
    if (defined('APP_ENV') && APP_ENV === 'development') {
        error_reporting(E_ALL);
        ini_set('display_errors', 1); // tampilkan error di browser
    } else {
        error_reporting(0);
        ini_set('display_errors', 0); // sembunyikan error di browser
    }
    
    // Header Security (keamanan parsing data)
    require_once __DIR__ . '/functions.php';
    
    require_once __DIR__ . '/security.php';
    require_once __DIR__ . '/security_headers.php';

    // Email Helper
    require_once __DIR__ . '/Notification.php';
    require_once __DIR__ . '/email_helper.php';
    
    // Helper
    require_once __DIR__ . '/../helper/notification_helper.php';
    require_once __DIR__ . '/../helper/image_helper.php';

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

    // ===================================================
    // CUSTOM ERROR & EXCEPTION HANDLER
    // ===================================================
    function custom_error_handler($errno, $errstr, $errfile, $errline) {
        if (APP_ENV === 'development') {
            custom_error_display("Error [$errno]", $errstr, $errfile, $errline);
        } else {
            // Log error ke file
            error_log("[$errno] $errstr in $errfile on line $errline", 3, __DIR__ . '/../storage/logs/error_log.txt');

            // Tampilkan halaman error ramah user
            production_friendly_page();
        }
        return true;
    }

    function custom_exception_handler($exception) {
        if (APP_ENV === 'development') {
            custom_error_display("Uncaught Exception", $exception->getMessage(), $exception->getFile(), $exception->getLine());
        } else {
            // Log exception ke file
            error_log("[Exception] " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine(), 3, __DIR__ . '/../storage/logs/error_log.txt');

            // Halaman fallback
            production_friendly_page();
        }
    }

    function production_friendly_page() {
        // Pastikan tidak ada output buffer tertinggal
        if (ob_get_length()) ob_end_clean();

        http_response_code(500);
        echo '
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: "Segoe UI", Roboto, sans-serif;
            }
            body {
                background: linear-gradient(135deg, #0a3d62, #3c6382);
                color: #fff;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
            }
            .error-container {
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(8px);
                padding: 50px 60px;
                border-radius: 20px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.25);
                max-width: 600px;
                animation: fadeIn 0.8s ease-in-out;
            }
            .error-icon {
                font-size: 60px;
                margin-bottom: 20px;
                color: #f8d7da;
            }
            .error-title {
                font-size: 28px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .error-message {
                font-size: 18px;
                color: #e0e0e0;
                margin-bottom: 30px;
            }
            .btn-refresh {
                background: #00a8ff;
                color: white;
                padding: 12px 25px;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                cursor: pointer;
                transition: 0.3s;
            }
            .btn-refresh:hover {
                background: #0097e6;
                transform: translateY(-2px);
            }
            .footer {
                margin-top: 25px;
                font-size: 14px;
                color: #c0c0c0;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>

        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <div class="error-title">Sistem Sedang Mengalami Kendala</div>
            <div class="error-message">
                Mohon maaf, saat ini sistem E-Hub BKD tidak dapat memproses permintaan Anda.<br>
                Tim teknis kami sudah diberitahu dan sedang memperbaikinya.
            </div>
            <button class="btn-refresh" onclick="location.reload()">🔄 Muat Ulang Halaman</button>
            <div class="footer">© ' . date('Y') . ' E-Hub BKD • Portal Layanan Kepegawaian</div>
        </div>';
        exit;
    }

    function custom_error_display($title, $message, $file, $line) {
        echo "
        <style>
            body { background:#f8f9fa; font-family:'Segoe UI', sans-serif; color:#333; margin:0; padding:40px; }
            .error-box { background:white; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); padding:30px; max-width:800px; margin:auto; }
            .error-title { font-size:24px; color:#dc3545; margin-bottom:10px; }
            .error-message { font-size:16px; margin-bottom:20px; }
            .error-file { font-family:monospace; color:#555; }
            .error-footer { margin-top:20px; font-size:14px; color:#888; }
        </style>
        <div class='error-box'>
            <div class='error-title'>🚨 {$title}</div>
            <div class='error-message'>{$message}</div>
            <div class='error-file'>File: {$file}<br>Line: {$line}</div>
            <div class='error-footer'>E-Hub BKD System • PHP Error Handler</div>
        </div><br>";
    }

    // Aktifkan custom handler
    set_error_handler('custom_error_handler');
    set_exception_handler('custom_exception_handler');

    // === Auto Sanitizer: Filter global untuk input ===
    if ((!defined('RAW_JSON_REQUEST') || RAW_JSON_REQUEST !== true) && isset($security['enable']) && $security['enable']) {
        function sanitize_array($arr)
        {
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
    
        if (!empty($_POST))    $_POST    = sanitize_array($_POST);
        if (!empty($_GET))     $_GET     = sanitize_array($_GET);
        if (!empty($_REQUEST)) $_REQUEST = sanitize_array($_REQUEST);
    }
    
        // if (isset($security['enable']) && $security['enable']) {
        //     // Recursive function untuk bersihkan input array
        //     // function sanitize_array($arr) {
        //     //     $cleaned = [];
        //     //     foreach ($arr as $key => $val) {
        //     //         if (is_array($val)) {
        //     //             $cleaned[$key] = sanitize_array($val);
        //     //         } else {
        //     //             $cleaned[$key] = secure_input($val);
        //     //         }
        //     //     }
        //     //     return $cleaned;
        //     // }
    
        //     // // Auto sanitize global variables
        //     // if (!empty($_POST))    $_POST    = sanitize_array($_POST);
        //     // if (!empty($_GET))     $_GET     = sanitize_array($_GET);
        //     // if (!empty($_REQUEST)) $_REQUEST = sanitize_array($_REQUEST);
    
        // }
