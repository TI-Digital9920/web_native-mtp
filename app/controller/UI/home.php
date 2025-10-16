<?php
require_once __DIR__ . '/../../system/autoload.php';
view('Head/header.php');
view('Topbar/navbar.php');
?>

<?php
// === CEK apakah encryption_key sudah diganti ===
if ($security['encryption_key'] === 'change_this_to_a_long_random_key_32_chars_min') {
    echo '
    <div class="alert alert-warning alert-fixed text-center mb-0" role="alert">
        ⚠️ <strong>Keamanan:</strong> Anda belum mengganti <code>encryption_key</code> di <code>config.php</code>. 
        <br>Segera ubah untuk menjaga keamanan data Anda.
    </div>';
}
?>

<!-- HERO -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold">Template PHP Native <span class="text-primary">Modern & Aman</span></h1>
                <p class="lead text-muted">Starter kit ringan untuk aplikasi web profesional. Sudah termasuk Bootstrap
                    5, sistem keamanan opsional, dan contoh form register/login.</p>
                <div class="mt-4">
                    <a href="<?= base_url('register') ?>" class="btn btn-primary btn-lg me-2">Coba
                        Registrasi</a>
                    <a href="<?= base_url('login') ?>" class="btn btn-outline-primary btn-lg">Login</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-card p-4 rounded-4 shadow-lg">
                    <img src="<?= base_url('assets/img/logo_utama.png') ?>" alt="illustration" style="max-width:260px;">
                    <p class="mt-3 text-muted">Ringan, mudah dimodifikasi, cocok untuk belajar dan produksi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                        <h5 class="card-title">Keamanan</h5>
                        <p class="card-text text-muted">XSS, CSRF, Password hashing, Prepared statements, Optional
                            encryption.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                        <h5 class="card-title">Modular</h5>
                        <p class="card-text text-muted">Struktur MVC ringan, mudah menambah halaman & library.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                        <h5 class="card-title">Responsive</h5>
                        <p class="card-text text-muted">Bootstrapped + mobile friendly dan aesthetic.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php view('Footer/footer.php'); ?>