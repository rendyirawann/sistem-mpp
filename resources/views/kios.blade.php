<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kios MPP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-200 h-screen w-screen flex flex-col lg:flex-row overflow-hidden">

    {{-- ================= LEFT INFO ================= --}}
    <div class="w-full lg:w-1/4 bg-blue-600 text-white p-6 flex flex-col">

        <div class="mb-4">
            <img src="{{ asset('images/logo_pemda.png') }}" alt="Mal Pelayanan Publik" class="w-24 lg:w-32 mx-auto">
        </div>

        <h1 class="text-xl lg:text-2xl font-bold mb-4 border-b pb-3 text-center">
            INFORMASI & LOKASI
        </h1>

        {{-- JAM LAYANAN --}}
        {{-- JAM & INFO LAYANAN --}}
        <div class="bg-blue-500 rounded-xl p-4 mb-6 shadow-inner border border-blue-400">
            {{-- Jam Real-time --}}
            <div class="text-center mb-3 border-b border-blue-400 pb-3">
                <p class="text-[10px] uppercase tracking-widest opacity-80 mb-1">Waktu Saat Ini</p>
                <div class="text-3xl font-mono font-bold leading-none" id="realtimeClock">00:00:00</div>
                <p class="text-xs mt-2 opacity-90" id="realtimeDate">Memuat tanggal...</p>
            </div>


            {{-- Info Layanan Statis --}}
            <div class="space-y-2 text-xs lg:text-sm pt-1">
                <div class="flex items-center justify-between">
                    <span><i class="fa fa-clock mr-2"></i>Jam Layanan:</span>
                    <span class="font-bold">08.00 – 15.00 WIB</span>
                </div>
                <div class="flex items-center justify-between">
                    <span><i class="fa fa-calendar mr-2"></i>Hari Kerja:</span>
                    <span class="font-bold">Senin – Jumat</span>
                </div>
            </div>
        </div>

         {{-- LOKASI STAND --}}
        <div class="space-y-4 text-sm lg:text-base">
            <div class="bg-white text-blue-700 rounded-xl p-4 shadow">
                <p class="font-bold flex items-center gap-2">
                    <i class="fa fa-building"></i> Polres
                </p>
                <p class="mt-1">⬅️ Sebelah Kiri Gedung</p>
            </div>

            <div class="bg-white text-blue-700 rounded-xl p-4 shadow">
                <p class="font-bold flex items-center gap-2">
                    <i class="fa fa-building"></i> Dinas Kominfostan
                </p>
                <p class="mt-1">➡️ Sebelah Kanan Gedung</p>
            </div>

            <div class="bg-white text-blue-700 rounded-xl p-4 shadow">
                <p class="font-bold flex items-center gap-2">
                    <i class="fa fa-building"></i> Bapenda
                </p>
                <p class="mt-1">⬆️ Ujung kanan Gedung</p>
            </div>

            <div class="bg-white text-blue-700 rounded-xl p-4 shadow">
                <p class="font-bold flex items-center gap-2">
                    <i class="fa fa-building"></i> PTSP
                </p>
                <p class="mt-1">⬅️ Ujung Kiri Gedung</p>
            </div>
        </div>
    </div>

    {{-- ================= MAIN ================= --}}
    <div class="w-full lg:w-3/4 p-6 lg:p-10 overflow-y-auto">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($skpd as $item)
                <button class="bg-white rounded-3xl shadow-lg hover:scale-105 transition p-6 text-center"
                    onclick="openLayanan('{{ $item->id }}')">
                    <i class="fa fa-building text-4xl text-blue-600 mb-3"></i>
                    <p class="font-bold">{{ $item->nama_skpd }}</p>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ================= MODAL LAYANAN ================= --}}
    <div id="modalLayanan" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-40">
        <div class="bg-white rounded-3xl w-full max-w-lg">
            <div class="bg-blue-600 text-white p-5 flex items-center">
                <button onclick="closeModal()" class="mr-4">
                    <i class="fa fa-arrow-left"></i>
                </button>
                <h2 id="judulSkpd" class="text-xl font-bold"></h2>
            </div>
            <div id="daftarLayanan" class="p-6 space-y-4"></div>
        </div>
    </div>

    {{-- ================= MODAL FORM ================= --}}
    <div id="modalForm" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-40">
        <div class="bg-white rounded-3xl w-full max-w-lg">
            <div class="bg-blue-600 text-white p-5 flex items-center">
                <button onclick="backToLayanan()" class="mr-4">
                    <i class="fa fa-arrow-left"></i>
                </button>
                <h2 id="judulLayanan" class="text-xl font-bold"></h2>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('ambil.antrian') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="skpd_id" id="inputSkpd">
                    <input type="hidden" name="loket_id" id="inputLoket">

                    <input name="nik" placeholder="NIK" class="w-full border p-3 rounded-xl" required>
                    <input name="nama" placeholder="Nama" class="w-full border p-3 rounded-xl" required>
                    <input name="no_hp" placeholder="No HP" class="w-full border p-3 rounded-xl" required>

                    <button class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold">
                        CETAK TIKET
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= POPUP TIKET ================= --}}
    @if (session('tiket'))
        <div id="popupTiket" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">

            <div class="bg-white rounded-3xl p-10 text-center w-80">
                <p class="text-gray-500 font-bold">NOMOR ANTRIAN ANDA</p>
                <h1 class="text-6xl font-black text-blue-600 my-6">
                    {{ session('tiket') }}
                </h1>

                <button onclick="goHome()" class="bg-blue-600 text-white px-8 py-3 rounded-full font-bold">
                    SELESAI
                </button>

                <p class="text-xs text-gray-400 mt-4">
                    Otomatis kembali dalam 5 detik
                </p>
            </div>
        </div>
    @endif

    {{-- ================= SCRIPT ================= --}}
    <script>
        const skpdData = @json($skpd);

        function openLayanan(id) {
            const skpd = skpdData.find(s => s.id === id);
            document.getElementById('judulSkpd').innerText = skpd.nama_skpd;

            const list = document.getElementById('daftarLayanan');
            list.innerHTML = '';

            skpd.lokets.forEach(loket => {
                const btn = document.createElement('button');
                btn.className = 'w-full bg-blue-50 p-4 rounded-xl flex justify-between';
                btn.innerHTML = `<span>${loket.nama_loket}</span><i class="fa fa-chevron-right"></i>`;
                btn.onclick = () => openForm(skpd.id, loket.id, loket.nama_loket);
                list.appendChild(btn);
            });

            document.getElementById('modalLayanan').classList.remove('hidden');
            document.getElementById('modalLayanan').classList.add('flex');
        }

        function openForm(skpdId, loketId, nama) {
            document.getElementById('modalLayanan').classList.add('hidden');
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalForm').classList.add('flex');

            document.getElementById('judulLayanan').innerText = nama;
            document.getElementById('inputSkpd').value = skpdId;
            document.getElementById('inputLoket').value = loketId;
        }

        function backToLayanan() {
            document.getElementById('modalForm').classList.add('hidden');
            document.getElementById('modalLayanan').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalForm').classList.add('hidden');
            document.getElementById('modalLayanan').classList.add('hidden');
        }

        function goHome() {
            window.location.href = "/";
        }

        // AUTO REDIRECT 5 DETIK
        @if (session('tiket'))
            setTimeout(() => {
                window.location.href = "/";
            }, 5000);
        @endif

        function updateClock() {
            const now = new Date();

            // Format Jam
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('realtimeClock').innerText = `${hours}:${minutes}:${seconds}`;

            // Format Tanggal (Bahasa Indonesia)
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const dateString = now.toLocaleDateString('id-ID', options);
            document.getElementById('realtimeDate').innerHTML = `<i class="fa fa-calendar mr-2"></i>${dateString}`;
        }

        // Jalankan fungsi setiap detik
        setInterval(updateClock, 1000);
        updateClock(); // Panggil langsung agar tidak nunggu 1 detik pertama
    </script>

</body>

</html>
