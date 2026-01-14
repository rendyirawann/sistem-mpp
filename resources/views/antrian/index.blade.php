<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Antrian MPP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- METRONIC -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">

    <style>
        /* ===========================
           BACKGROUND FIX (UTAMA)
        ============================ */
        html,
        body {
            height: 100%;
        }

        body {
            background-image: url('{{ asset(' storage/images/1.png') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }

        /* ===========================
           WRAPPER
        ============================ */
        .antrian-wrapper {
            min-height: 100vh;
            padding: 40px;
            display: flex;
            gap: 40px;
        }

        /* ===========================
           PANEL INFORMASI
        ============================ */
        .panel-info {
            width: 340px;
            background: linear-gradient(180deg, #2563eb, #1e3a8a);
            border-radius: 32px;
            padding: 32px;
            color: #fff;
            box-shadow: 0 30px 60px rgba(0, 0, 0, .35);
        }

        .panel-info-box {
            margin-top: 20px;
            background: rgba(255, 255, 255, .25);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 24px;
            color: #000;
            min-height: 65vh;
        }

        /* ===========================
           PANEL GRID (GLASS)
        ============================ */
        .panel-grid {
            flex: 1;
            background: rgba(255, 255, 255, .25);
            backdrop-filter: blur(16px);
            border-radius: 42px;
            padding: 42px;
            box-shadow: 0 40px 80px rgba(0, 0, 0, .35);
            position: relative;
        }

        /* ===========================
           BUTTON GRID (TRANSPARAN)
        ============================ */
        .btn-grid {
            background: rgba(255, 255, 255, .45);
            backdrop-filter: blur(14px);
            border-radius: 26px;
            height: 120px;
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, .25),
                inset 0 0 0 2px rgba(255, 255, 255, .5);
            transition: .25s;
        }

        .btn-grid i {
            font-size: 34px;
            color: #2563eb;
            display: block;
            margin-bottom: 6px;
        }

        .btn-grid:hover {
            transform: translateY(-6px) scale(1.06);
            background: rgba(255, 255, 255, .65);
            box-shadow: 0 20px 50px rgba(37, 99, 235, .5);
        }
    </style>
</head>

<body id="kt_body" class="app-blank">

    <div class="antrian-wrapper">

        <!-- INFORMASI -->
        <div class="panel-info">
            <h1 class="fw-bold mb-5">INFORMASI</h1>
            <div class="panel-info-box">
                <ul class="fw-semibold">
                    <li>📌 Pilih layanan sesuai kebutuhan</li>
                    <li>📝 Masukkan data pengunjung</li>
                    <li>🔊 Tunggu nomor dipanggil</li>
                </ul>
            </div>
        </div>

        <!-- GRID -->
        <div class="panel-grid">
            <div class="row g-6">
                @for ($i = 1; $i <= 24; $i++)
                    <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6">
                    <button class="btn btn-grid w-100">
                        <i class="ki-outline ki-screen"></i>
                        {{ $i }}
                    </button>
            </div>
            @endfor
        </div>
    </div>

    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

</body>

</html>
