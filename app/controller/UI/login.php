<?php
// controller/UI/login.php
// Login handler yang sudah diperkuat:
// - CSRF protection
// - Input sanitization (secure_input)
// - Prepared statement (prepared_query) untuk mencegah SQL Injection
// - Validasi format email
// - Brute-force defense (session-based)
// - Session fixation protection (session_regenerate_id)
// - Penjelasan setiap blok ada pada komentar

require_once __DIR__ . '/../../system/autoload.php';

redirect_if_logged_in(); // 🔥 tendang user yang sudah login

view('Head/header.php');
view('Topbar/navbar.php');


// -----------------------------
// Konfigurasi kebijakan login
// -----------------------------
// Kamu bisa ubah nilai di bawah sesuai kebutuhan
$MAX_LOGIN_ATTEMPTS = 5;        // maksimal percobaan login sebelum dikunci sementara
$LOCKOUT_SECONDS    = 300;      // lama waktu lockout dalam detik (contoh: 300 = 5 menit)

// Pastikan session aktif karena kita menyimpan jumlah percobaan login di session
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Inisialisasi struktur session untuk tracking login attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['login_last_attempt'])) {
    $_SESSION['login_last_attempt'] = null;
}

// Jika user sedang dalam masa lockout, cek dan beri pesan
if (!empty($_SESSION['login_last_attempt'])) {
    $elapsed = time() - (int)$_SESSION['login_last_attempt'];
    if ($_SESSION['login_attempts'] >= $MAX_LOGIN_ATTEMPTS && $elapsed < $LOCKOUT_SECONDS) {
        $remaining = $LOCKOUT_SECONDS - $elapsed;
        // Format sisa waktu ke menit/detik
        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;
        alert("Terlalu banyak percobaan login. Coba lagi dalam {$minutes} menit {$seconds} detik.", 'danger');
        // Tampilkan form tetap (bisa juga mem-block form dengan kondisi ini)
    }
}

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ------------------------------------------
    // 1) Verifikasi CSRF (jika fitur CSRF aktif)
    // ------------------------------------------
    if (!empty($security['csrf']) && !verify_csrf($_POST['_csrf_token'] ?? '')) {
        // Tambahkan log atau tindakan lain jika diperlukan
        alert('Token CSRF tidak valid.', 'danger');
    } else {
        // ------------------------------------------
        // 2) Cek lockout lagi (untuk kasus POST)
        // ------------------------------------------
        $elapsed = !empty($_SESSION['login_last_attempt']) ? time() - (int)$_SESSION['login_last_attempt'] : 999999;
        if ($_SESSION['login_attempts'] >= $MAX_LOGIN_ATTEMPTS && $elapsed < $LOCKOUT_SECONDS) {
            $remaining = $LOCKOUT_SECONDS - $elapsed;
            $minutes = floor($remaining / 60);
            $seconds = $remaining % 60;
            alert("Akun terkunci sementara. Coba lagi dalam {$minutes} menit {$seconds} detik.", 'danger');
        } else {
            // Jika lockout sudah melewati waktu, reset counter agar user bisa mencoba lagi
            if ($elapsed >= $LOCKOUT_SECONDS) {
                $_SESSION['login_attempts'] = 0;
                $_SESSION['login_last_attempt'] = null;
            }

            // ------------------------------------------
            // 3) Ambil & sanitasi input
            // ------------------------------------------
            // Note: autoload sudah melakukan sanitasi global, tapi kita tetap gunakan secure_input
            $email = secure_input($_POST['email'] ?? '');
            $password = $_POST['password'] ?? ''; // password jangan di-escape karena akan diverifikasi

            // ------------------------------------------
            // 4) Validasi format email (client+server)
            // ------------------------------------------
            if (empty($email) || empty($password)) {
                alert('Email dan password wajib diisi.', 'warning');
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                alert('Format email tidak valid.', 'warning');
            } else {
                // ------------------------------------------
                // 5) Ambil user dari DB (prepared statement)
                // ------------------------------------------
                $sql = "SELECT id, uuid, username, password FROM pengguna WHERE email = ? LIMIT 1";
                $res = prepared_query($conn, $sql, 's', [$email]);

                if ($res && count($res) > 0) {
                    $user = $res[0];

                    // ------------------------------------------
                    // 6) Verifikasi password
                    // ------------------------------------------
                    if (verify_password($password, $user['password'])) {
                        // SUCCESS: reset counter percobaan login
                        $_SESSION['login_attempts'] = 0;
                        $_SESSION['login_last_attempt'] = null;

                        // Pastikan session aktif dan regenerasi session id untuk mencegah session fixation
                        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
                        session_regenerate_id(true); // *** PENTING: session fixation protection ***

                        // Simpan data user ke session
                        $_SESSION['user_uuid'] = $user['uuid'];
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];

                        $_SESSION['login'] = true; // 🔥 Tandai user sudah login dengan aktifkan session

                        // (Opsional) simpan timestamp login terakhir
                        $_SESSION['last_login_at'] = time();

                        // update status akun jadi Online dan login_terakhir
                        prepared_query($conn, "UPDATE pengguna SET status_akun = 'Online', login_terakhir = NOW() WHERE id = ?", 'i', [$user['id']]);

                        // catat aktivitas login
                        prepared_query($conn, "INSERT INTO log_activity (pengguna_uuid, tipe_aktivitas, aktivitas_detail, ip_public) VALUES (?, ?, ?, ?)", 'ssss', [
                            $user['uuid'], 'login', 'Berhasil login via form', $_SERVER['REMOTE_ADDR'] ?? ''
                        ]);

                        alert('Login berhasil', 'success');

                        // Redirect ke halaman tujuan (gunakan base route)
                        // Gunakan redirect() helper agar base_url otomatis ditambahkan
                        // redirect ke panel-dashboard
                        redirect('panel-dashboard');

                        // Pastikan tidak ada eksekusi lebih lanjut
                        exit;
                    } else {
                        // Password salah -> increment counter
                        $_SESSION['login_attempts'] += 1;
                        $_SESSION['login_last_attempt'] = time();

                        $attempts_left = max(0, $MAX_LOGIN_ATTEMPTS - $_SESSION['login_attempts']);
                        if ($attempts_left === 0) {
                            alert("Password salah. Anda telah melewati batas percobaan. Akun terkunci sementara selama {$LOCKOUT_SECONDS} detik.", 'danger');
                        } else {
                            alert("Password salah. Tersisa {$attempts_left} percobaan.", 'danger');
                        }
                    }
                } else {
                    // Email tidak ditemukan -> increment counter juga (jangan berikan info terlalu detail)
                    $_SESSION['login_attempts'] += 1;
                    $_SESSION['login_last_attempt'] = time();

                    $attempts_left = max(0, $MAX_LOGIN_ATTEMPTS - $_SESSION['login_attempts']);
                    alert("Email/Password salah. Tersisa {$attempts_left} percobaan.", 'warning');
                }
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3>Login</h3>
                    <form method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <!-- value diisi kembali dari $_POST jika ingin UX lebih baik -->
                            <input name="email" type="email" class="form-control" required
                                value="<?= isset($_POST['email']) ? escape_output($_POST['email']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input name="password" type="password" class="form-control" required>
                        </div>
                        <button class="btn btn-primary">Masuk</button>
                    </form>

                    <hr>
                    <small class="text-muted">Belum punya akun? <a href="<?= base_url('register') ?>">Daftar</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('Footer/footer.php'); ?>