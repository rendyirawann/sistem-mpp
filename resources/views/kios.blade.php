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

<body class="bg-gray-200 h-screen w-screen flex overflow-hidden">

{{-- ================= LEFT INFO ================= --}}
<div class="w-1/4 bg-blue-600 text-white p-6">
    <h1 class="text-3xl font-bold mb-6 border-b pb-3 text-center">INFORMASI</h1>

    <div class="bg-blue-500 rounded-xl p-5 space-y-3">
        <p><i class="fa fa-clock mr-2"></i>08.00 – 15.00 WIB</p>
        <p><i class="fa fa-calendar mr-2"></i>Senin – Jumat</p>
        <p><i class="fa fa-info-circle mr-2"></i>Pilih instansi tujuan Anda</p>
    </div>
</div>

{{-- ================= MAIN ================= --}}
<div class="w-3/4 p-10 relative">

    {{-- GRID SKPD --}}
    <div class="grid grid-cols-4 gap-8">
        @foreach ($skpd as $item)
            <button
                class="bg-white rounded-3xl shadow-lg hover:scale-105 transition p-6 text-center"
                onclick="openLayanan('{{ $item->id }}')"
            >
                <i class="fa fa-building text-4xl text-blue-600 mb-3"></i>
                <p class="font-bold text-gray-700">
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

        {{-- HEADER --}}
        <div class="bg-blue-600 text-white p-5 flex items-center">
            <button onclick="closeModal()" class="mr-4">
                <i class="fa fa-arrow-left text-xl"></i>
            </button>
            <h2 id="judulSkpd" class="text-xl font-bold"></h2>
        </div>

        {{-- BODY --}}
        <div id="daftarLayanan" class="p-6 space-y-4">
            {{-- DIISI VIA JS --}}
        </div>
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

{{-- ================= TIKET ================= --}}
@if(session('tiket'))
<div class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50">
    <div class="bg-white p-10 rounded-3xl text-center">
        <p class="text-gray-500 font-bold">NOMOR ANTRIAN ANDA</p>
        <h1 class="text-6xl font-black text-blue-600 my-4">
            {{ session('tiket') }}
        </h1>
        <a href="/" class="bg-blue-600 text-white px-6 py-3 rounded-full">
            AMBIL ANTRIAN
        </a>
    </div>
</div>
@endif
{{-- ================= ERROR VALIDATION ================= --}}

@if ($errors->any())
<div class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50" id="errorModal">
    <div class="bg-white p-8 rounded-3xl text-center max-w-md w-full mx-4 shadow-2xl">
        <div class="flex justify-center mb-4">
            <div class="bg-red-100 p-4 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </div>

        <h2 class="text-2xl font-black text-gray-800 mb-2">GAGAL</h2>
        <p class="text-gray-500 mb-6 text-sm">Mohon periksa kembali data Anda:</p>

        <div class="bg-red-50 border border-red-100 rounded-xl p-4 mb-6 text-left">
            <ul class="list-disc list-inside text-red-600 text-sm font-medium space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

        <button onclick="document.getElementById('errorModal').style.display='none'" 
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-full transition duration-200">
            TUTUP
        </button>
    </div>
</div>
@endif
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
