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

    {{-- LOGO --}}
    <div class="mb-4">
        <img 
            src="{{ asset('images/logo-mpp.png') }}" 
            alt="Mal Pelayanan Publik"
            class="w-24 lg:w-32 mx-auto"
        >
    </div>

    <h1 class="text-xl lg:text-2xl font-bold mb-4 border-b pb-3 text-center">
        INFORMASI & LOKASI
    </h1>

    {{-- JAM LAYANAN --}}
    <div class="bg-blue-500 rounded-xl p-4 mb-6 space-y-2 text-sm lg:text-base">
        <p><i class="fa fa-clock mr-2"></i>08.00 – 15.00 WIB</p>
        <p><i class="fa fa-calendar mr-2"></i>Senin – Jumat</p>
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
<div class="w-full lg:w-3/4 p-6 lg:p-10 relative overflow-y-auto">

    {{-- GRID SKPD --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
        @foreach ($skpd as $item)
            <button
                class="bg-white rounded-3xl shadow-lg hover:scale-105 transition p-6 text-center"
                onclick="openLayanan('{{ $item->id }}')"
            >
                <i class="fa fa-building text-3xl lg:text-4xl text-blue-600 mb-3"></i>
                <p class="font-bold text-gray-700 text-sm lg:text-base">
                    {{ $item->nama_skpd }}
                </p>
            </button>
        @endforeach
    </div>
</div>

{{-- ================= MODAL LAYANAN ================= --}}
<div id="modalLayanan"
     class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-xl overflow-hidden">
        <div class="bg-blue-600 text-white p-5 flex items-center">
            <button onclick="closeModal()" class="mr-4">
                <i class="fa fa-arrow-left text-xl"></i>
            </button>
            <h2 id="judulSkpd" class="text-xl font-bold"></h2>
        </div>
        <div id="daftarLayanan" class="p-6 space-y-4"></div>
    </div>
</div>

{{-- ================= MODAL FORM ================= --}}
<div id="modalForm"
     class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-xl overflow-hidden">
        <div class="bg-blue-600 text-white p-5 flex items-center">
            <button onclick="backToLayanan()" class="mr-4">
                <i class="fa fa-arrow-left text-xl"></i>
            </button>
            <h2 id="judulLayanan" class="text-xl font-bold"></h2>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('ambil.antrian') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="skpd_id" id="inputSkpd">
                <input type="hidden" name="loket_id" id="inputLoket">

                <div>
                    <label>NIK</label>
                    <input type="text" name="nik" required class="w-full border p-3 rounded-xl">
                </div>

                <div>
                    <label>Nama</label>
                    <input type="text" name="nama" required class="w-full border p-3 rounded-xl">
                </div>

                <div>
                    <label>No HP</label>
                    <input type="text" name="no_hp" required class="w-full border p-3 rounded-xl">
                </div>

                <button class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold">
                    CETAK TIKET
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
    const skpdData = @json($skpd);

    function openLayanan(skpdId) {
        const skpd = skpdData.find(s => s.id === skpdId);
        document.getElementById('judulSkpd').innerText = skpd.nama_skpd;

        const container = document.getElementById('daftarLayanan');
        container.innerHTML = '';

        if (skpd.lokets.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500">Belum ada layanan</p>';
        }

        skpd.lokets.forEach(loket => {
            const btn = document.createElement('button');
            btn.className = 'w-full bg-blue-50 p-4 rounded-xl flex justify-between items-center hover:bg-blue-100';
            btn.innerHTML = `<span>${loket.nama_loket}</span><i class="fa fa-chevron-right"></i>`;
            btn.onclick = () => openForm(skpd.id, loket.id, loket.nama_loket);
            container.appendChild(btn);
        });

        document.getElementById('modalLayanan').classList.remove('hidden');
        document.getElementById('modalLayanan').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('modalLayanan').classList.add('hidden');
        document.getElementById('modalForm').classList.add('hidden');
    }

    function openForm(skpdId, loketId, namaLayanan) {
        document.getElementById('modalLayanan').classList.add('hidden');
        document.getElementById('modalForm').classList.remove('hidden');
        document.getElementById('modalForm').classList.add('flex');

        document.getElementById('judulLayanan').innerText = namaLayanan;
        document.getElementById('inputSkpd').value = skpdId;
        document.getElementById('inputLoket').value = loketId;
    }

    function backToLayanan() {
        document.getElementById('modalForm').classList.add('hidden');
        document.getElementById('modalLayanan').classList.remove('hidden');
    }
</script>

</body>
</html>
