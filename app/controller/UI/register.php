<?php
// sistem keamanan otomatis
require_once __DIR__ . '/../../system/autoload.php';

redirect_if_logged_in(); // 🔥 tendang user yang sudah login

view('Head/header.php');
view('Topbar/navbar.php');

// panggil file logic-nya
require_once __DIR__ . '/../../controller/Insert/pengguna.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="mb-3">Registrasi</h3>
                    <form method="post" action="">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input name="password" type="password" class="form-control" required>
                        </div>
                        <button class="btn btn-primary">Daftar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('Footer/footer.php'); ?>