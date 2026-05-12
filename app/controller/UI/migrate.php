<?php
// app/controller/UI/migrate.php
require_once __DIR__ . '/../../system/autoload.php';
require_once __DIR__ . '/../../system/migrate.php';

view('Head/header.php');
view('Topbar/navbar.php');

// keamanan: hanya izinkan di dev atau jika session admin // akses di http://localhost/siarsip/public/?url=migrate
$allow = (defined('APP_ENV') && APP_ENV === 'development');
// kamu bisa ganti $allow=> check session user role admin

$migrator = new MigrationManager($conn, __DIR__ . '/../../system/migrations');
$files = $migrator->getMigrationFiles();
$executed = $migrator->getExecuted();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $allow) {
    // jelaskan: CSRF check terserah di projectmu (gunakan verify_csrf())
    $results = $migrator->runPending();
}
?>

<div class="container py-5">
    <div class="card shadow">
        <div class="card-body">
            <h3>Migrations</h3>
            <?php if (!$allow): ?>
            <div class="alert alert-warning">UI migrasi hanya aktif di mode development.</div>
            <?php endif; ?>

            <form method="post">
                <?php if ($allow): ?>
                <button class="btn btn-primary mb-3">Jalankan Migrasi Pending</button>
                <?php endif; ?>
            </form>

            <?php if (!empty($results)): ?>
            <h5>Hasil</h5>
            <pre><?= htmlspecialchars(print_r($results, true)) ?></pre>
            <?php endif; ?>

            <h5>Daftar Migrasi</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Migration</th>
                        <th>Last Modified</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $name => $info): ?>
                    <tr>
                        <td><?= htmlspecialchars($name) ?></td>
                        <td><?= date('Y-m-d H:i:s', filemtime($info['file'])) ?></td>
                        <td>
                            <?= in_array($name, $executed) ? '<span class="badge bg-success">Executed</span>' : '<span class="badge bg-secondary">Pending</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php view('Footer/footer.php'); ?>