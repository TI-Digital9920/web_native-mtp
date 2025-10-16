# PHP Native Template - Mohammad Tri Putra

## Ringkasan
- Struktur modular (controller, view, system, assets)
- Optional security helper (XSS, CSRF, password hashing, prepared statements, encryption)
- Halaman Default : home, register, login dan panel dahboard
- Bootstrap 5 + responsive design lengkap dengan alert SWEATALERT2

# Setup Awal (Install/Download Project)
## Persiapan
- Install text editor (Sublime Teks, Notepad++, Visual Studio Code, Dll) - VSCode (rekomnedasi)
- Install XAMPP untuk dan mysql minimal 8.1
- Install Composer Versi 2.8.8 (Min)
- Install NodeJS Versi 22.20.0 (Min)
- Install Git Versi 2.51.0 (Min)

## Instal/Download Teamplate
- Download Extension Di VSCode (gunakan extension yang anda perlukan minimal untuk code formatter)
- Buat folder baru misalnya di `D:\contoh_project\` atau bisa langsung di dalam folder `htdocs` anda
- Klik kanan pilih Open Git Bash Here
- Tuliskan perintah `composer .....`
- Tunggu prosesnnya hingga benar-benar selesai
- Setelah selesai, Pindahkan folder projectnya kedalam `htdocs` (jika folder intallasi dilakukan di luar `htdoc`)
- Jalankan `apache` dan `mysql` di `XAMPP`, kemudia buka `phpmyadmin` dengan menggunakan URL `http://localhost/phpmyadmin/` atau anda bisa juga langsung klik 'admin' di `mysql` yang ada pada `XAMPP`
- Buat database baru misalnya `myproject' (tidak perlu buat tablenya, karena kita akan membuat table melalui file migrasi yang ada di dalam teamplate)

## Configurasi Project Awal
- Buka VSCode anda, lalu open folder project anda
- Setelah terbuka, langkah awal pergi ke file `.ENV` atau file `config.php` yang ada di `app/config/config.php`
- Sesuaikan nama `Database` yang ada di dalam file ini dengan nama `Database` yang anda buat `phpmyadmin`
- Ikuti arahan yang ada di dalam file `config.php` sesuaikan (semua konfigurasi config dijelaskan melalui komentar-komentar yang ada di file tersebut)
- Setelah file config di setup, lakukan sedikit perubahan di semua file `.htaccess` sesuaikan nama projectnya
- Buka browser tuliskan URL project anda `http://localhost/nama_project` misalnya `http://localhost/myproject`
- DONE ~ Project anda sekarang sudah siap untuk di modifikasi sesuai kebutuhan.

- Catatan : Jika terjadi error, coba perhatikan kembali file `.ENV` atau file `config.php` yang ada di `app/config/config.php` serta file `.htaccess` anda. Untuk project yang pertama kali di jalankan, sistem akan langsung otomatis melakukan `migrasi-table` kedalan `Database` anda

# Isi paket:
- Beberapa file migrasi (pengguna, table_baru template, log_activity, reset_password_token, data1)
- UI controllers untuk Dashboard
- Routes yang terintegrasi
- Petunjuk penambahan helper
- Petunjuk penambahan footer & navbar snippet

### 📝 Keterangan

- **`app/`** → Berisi file utama aplikasi (Controller, security, function, migrasi, dll).  
- **`public/`** → Folder publik untuk file frontend seperti assets (CSS, JS, img, dan library lainnya) entry point `index.php`.  
- **`vendor/`** → Folder hasil dari Composer (`composer install`).  
- **`.htaccess`** → Mengatur routing URL.  
- **`composer.json`** → Berisi informasi dan dependensi project PHP kamu.  
- **`composer.lock`** → Mengunci versi dependensi yang digunakan.  
- **`README.md`** → Dokumentasi utama project ini.

- Opsional, anda dapat menhapus folder `vendor/` dan file `composer.json` dan `composer.lock`, project tetap akan jalan.


# Selamat mencoba!
