<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Unlock Kiosk - MPP Deli Serdang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/logo_deliserdang.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f7f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            width: 60px;
            margin-bottom: 20px;
        }

        .btn-unlock {
            background: linear-gradient(135deg, #009ef7 0%, #0069d9 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-unlock:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 158, 247, 0.4);
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <img src="{{ asset('images/logo_pemda.png') }}" alt="Logo" class="logo">
        <h2 class="fw-bolder text-dark mb-2">Kiosk Authorized</h2>
        <p class="text-muted fs-7 mb-8">Perangkat ini belum terverifikasi. Masukkan Kode Rahasia untuk membuka layanan Kiosk Antrian.</p>

        @if(session('error'))
            <div class="alert alert-danger p-3 fs-8 mb-5">
                <i class="fa fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('kiosk.verify') }}" method="POST">
            @csrf
            <div class="mb-5">
                <input type="password" name="kode" class="form-control form-control-solid text-center fs-3 ls-5" 
                       placeholder="••••••••" required autofocus autocomplete="off">
            </div>
            <button type="submit" class="btn btn-unlock w-100">
                UNLOCK PERANGKAT
            </button>
        </form>

        <div class="mt-8">
            <a href="{{ route('landing') }}" class="text-muted fs-9 fw-bold text-uppercase ls-1">Kembali ke Beranda</a>
        </div>
    </div>
</body>

</html>
