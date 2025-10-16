<?php
// app/controller/UI/dashboard.php
require_once __DIR__ . '/../../system/autoload.php';

if (empty($_SESSION['user_uuid']) || empty($_SESSION['login']) || $_SESSION['login'] !== true) {
    redirect('login');
}


view('Head/header.php');
view('Topbar/navbar.php');
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Panel Dashboard</h2>
        <div>
            <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('dashboard-input1') ?>">Input Data 1</a>
            <a class="btn btn-sm btn-outline-primary" href="<?= base_url('dashboard-table1') ?>">Table Data 1</a>
            <a class="btn btn-sm btn-danger" href="<?= base_url('logout') ?>">Logout</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <h5><?= escape_output($_SESSION['username'] ?? '') ?></h5>
                    <small class="text-muted">Role: <?= escape_output($_SESSION['role'] ?? 'pengguna') ?></small>
                    <hr>
                    <a href="<?= base_url('profile-edit') ?>" class="btn btn-sm btn-outline-primary">Edit Profile</a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <p>Selamat datang di dashboard. Gunakan menu untuk navigasi.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php view('Footer/footer.php'); ?>