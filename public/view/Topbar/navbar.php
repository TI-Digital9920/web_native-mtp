<?php if (!empty($_SESSION['username'])): ?>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button"
        data-bs-toggle="dropdown"><?= escape_output($_SESSION['username']) ?></a>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="<?= base_url('profile-edit') ?>">Update Profile</a></li>
        <li><a class="dropdown-item" href="<?= base_url('profile-password') ?>">Ubah Kata Sandi</a></li>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">Logout</a></li>
    </ul>
</li>
<?php else: ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= base_url() ?>">Nama Apps</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="<?= base_url('home') ?>">Contoh Menu 1</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contoh Menu 2</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contoh Menu 3</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>