<?php
/**
 * app/route/routes.php
 * Semua daftar route URL dan lokasi file controller-nya
 */

return [
    // === ROUTE UI (halaman tampilan) ===
    'home'      => 'app/controller/UI/home.php',      // localhost/myproject/home (ini alamat yang tampil di URL)
    'login'     => 'app/controller/UI/login.php',     // localhost/myproject/login (ini alamat yang tampil di URL)
    'register'  => 'app/controller/UI/register.php',  // localhost/myproject/register (ini alamat yang tampil di URL)
    'migrate'   => 'app/controller/UI/migrate.php',   // localhost/myproject/migrate (ini alamat yang tampil di URL)


    // Dashboard & profile
    'panel-dashboard' => 'app/controller/UI/dashboard.php',
    'logout' => 'app/system/logout.php',
    'profile-edit' => 'app/controller/UI/profile_edit.php',
    'profile-password' => 'app/controller/UI/profile_password.php',

    // Dashboard pages (CRUD contoh)
    'dashboard-input1' => 'app/controller/UI/dashboard_input1.php',
    'dashboard-table1' => 'app/controller/UI/dashboard_table1.php',

    // === ROUTE LOGIC (Insert, Update, Delete) ===
    // Tidak ada kata 'insert', 'update', atau 'delete' di URL
    'pengguna'          => 'app/controller/Insert/pengguna.php', 
    'update-pengguna'   => 'app/controller/Update/pengguna.php',
    'hapus-pengguna'    => 'app/controller/Delete/pengguna.php',

    // === DEFAULT ROUTE ===
    '' => 'app/controller/UI/home.php',  //localhost/myproject/home (Yang tampil pertama kali saat project dijalankan)
];