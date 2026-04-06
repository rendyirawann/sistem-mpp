<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Gangguan - Survey Kepuasan Masyarakat</title>

    {{-- ASSET METRONIC --}}
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/logo_deliserdang.png') }}" />

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f5f8fa;
        }
    </style>
</head>

<body id="kt_body" class="app-blank bgi-size-cover bgi-position-center bgi-no-repeat">

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid flex-center p-10">

            {{-- CONTAINER UTAMA --}}
            <div class="card card-flush w-100 mw-800px shadow-lg border-0 rounded-4">

                {{-- HEADER MERAH (DANGER) --}}
                <div
                    class="card-header bg-danger py-7 d-flex justify-content-center align-items-center flex-column rounded-top-4">
                    <h1 class="text-white fw-bolder fs-2x mb-1">PEMBERITAHUAN</h1>
                    <span class="text-white opacity-75 fs-6 fw-bold">MPP Kabupaten Deli Serdang</span>
                </div>

                <div class="card-body p-lg-20 p-10 text-center">
                    <div class="mb-10">
                        <i class="ki-outline ki-wifi-slash fs-5x text-danger mb-5"></i>
                    </div>
                    
                    <h2 class="fw-bolder text-dark mb-4">Mohon Maaf, Sistem Sedang Gangguan</h2>
                    <div class="fw-semibold fs-5 text-gray-500 mb-10">
                        Kami tidak dapat terhubung ke Server Pusat Layanan (Sukma Deli) saat ini.<br/>
                        Silakan tunggu beberapa saat atau hubungi petugas kami untuk informasi lebih lanjut.
                    </div>
                    
                    <a href="{{ route('skm.index') }}" class="btn btn-primary fw-bold px-8 py-3">
                        <i class="ki-outline ki-arrows-circle fs-2 me-2"></i> Coba Lagi Saat Ini
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS METRONIC --}}
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
</body>

</html>
