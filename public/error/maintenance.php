<?php
// error/maintenance.php
http_response_code(503);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #0d6efd, #6610f2, #6f42c1);
        background-size: 300% 300%;
        animation: gradientMove 6s ease infinite;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        overflow: hidden;
        font-family: "Poppins", sans-serif;
    }

    @keyframes gradientMove {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .maintenance-container {
        text-align: center;
        animation: fadeIn 1.2s ease;
        padding: 2rem;
    }

    .maintenance-icon {
        font-size: 6rem;
        color: #ffc107;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .maintenance-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-top: 1rem;
    }

    .maintenance-text {
        font-size: 1.1rem;
        margin-top: 0.5rem;
        color: #f8f9fa;
    }

    .countdown {
        font-size: 1.5rem;
        font-weight: bold;
        color: #ffc107;
        margin-top: 1.5rem;
        letter-spacing: 2px;
    }

    a.btn-home {
        margin-top: 2rem;
        background-color: #ffc107;
        color: #000;
        border: none;
        border-radius: 50px;
        padding: 0.8rem 2rem;
        transition: all 0.3s ease;
    }

    a.btn-home:hover {
        background-color: #fff;
        color: #000;
        transform: scale(1.05);
    }

    footer {
        position: absolute;
        bottom: 15px;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .maintenance-title {
            font-size: 1.8rem;
        }

        .maintenance-icon {
            font-size: 4.5rem;
        }

        .countdown {
            font-size: 1.2rem;
        }
    }
    </style>
</head>

<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">
            🚧
        </div>
        <div class="maintenance-title">Sistem Sedang Maintenance</div>
        <div class="maintenance-text">Kami sedang melakukan peningkatan sistem agar lebih cepat dan stabil. Mohon
            bersabar ya 😄</div>

        <div class="countdown" id="countdown">00:00:00</div>

        <a href="/siarsip" class="btn-home d-inline-block mt-3">Kembali ke Beranda</a>
    </div>

    <footer>
        © <?= date('Y') ?> SIARSIP — Semua Hak Dilindungi
    </footer>

    <script>
    // Set waktu maintenance selesai (misal +2 jam dari sekarang)
    const targetTime = new Date().getTime() + (2 * 60 * 60 * 1000);

    const countdown = document.getElementById("countdown");
    const timer = setInterval(() => {
        const now = new Date().getTime();
        const distance = targetTime - now;

        if (distance <= 0) {
            clearInterval(timer);
            countdown.textContent = "Sistem Sudah Aktif ✅";
            countdown.style.color = "#00ff88";
            return;
        }

        const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((distance / (1000 * 60)) % 60);
        const seconds = Math.floor((distance / 1000) % 60);

        countdown.textContent =
            `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
    }, 1000);
    </script>
</body>

</html>