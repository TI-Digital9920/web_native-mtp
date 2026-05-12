<?php
// error/500.php
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: radial-gradient(circle at top left, #0b5ed7, #000);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        overflow: hidden;
    }

    .error-container {
        text-align: center;
        animation: fadeIn 1.2s ease;
    }

    .error-code {
        font-size: 8rem;
        font-weight: 800;
        color: #0dcaf0;
        text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
    }

    .error-message {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    a.btn {
        background-color: #0dcaf0;
        color: #000;
        border: none;
        transition: all 0.3s ease;
    }

    a.btn:hover {
        background-color: #fff;
        color: #000;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <div class="error-message">Oops! Terjadi kesalahan di sisi server.</div>
        <p class="text-light mb-4">Coba lagi nanti, atau hubungi admin jika masalah berlanjut.</p>
        <a href="/siarsip" class="btn px-4 py-2 rounded-pill">Kembali ke Beranda</a>
    </div>
</body>

</html>