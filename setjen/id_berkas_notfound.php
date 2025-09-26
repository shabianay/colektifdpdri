<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkas Tidak Ditemukan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .icon {
            font-size: 80px;
            color: #ff6b6b;
            margin-bottom: 20px;
        }

        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
        }

        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            background-color: #4e73df;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #3a56c4;
        }

        .btn:active {
            transform: translateY(0);
        }

        .loader {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
            vertical-align: middle;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .countdown {
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">❌</div>
        <h1>ID Berkas Tidak Ditemukan</h1>
        <p>Maaf, ID berkas yang Anda cari tidak valid atau tidak ditemukan dalam sistem. Silakan kembali ke halaman daftar berkas dan coba lagi.</p>

        <a href="daftar_berkas_masuk.php" class="btn" id="redirectBtn">
            <span class="loader" id="loader"></span>
            Kembali ke Daftar Berkas
        </a>

        <div class="countdown" id="countdown">Redirect otomatis dalam 5 detik</div>
    </div>

    <script>
        // Hitung mundur untuk redirect otomatis
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        const redirectBtn = document.getElementById('redirectBtn');
        const loader = document.getElementById('loader');

        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = `Redirect otomatis dalam ${countdown} detik`;

            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = 'daftar_berkas_masuk.php';
            }
        }, 1000);

        // Sembunyikan loader setelah halaman dimuat
        window.addEventListener('load', function() {
            setTimeout(() => {
                loader.style.display = 'none';
            }, 1000);
        });
    </script>
</body>

</html>