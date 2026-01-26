<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kios MPP - Antrian & Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Scrollbar hanya custom di layar besar */
        @media (min-width: 1024px) {
            ::-webkit-scrollbar {
                width: 6px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f5f9;
            }

            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        }

        /* Animasi kedip halus untuk titik dua pada jam */
        .blink {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
    </style>
</head>

{{-- 
    PERBAIKAN RESPONSIVE DI BODY:
    1. min-h-screen: Agar tinggi minimal selayar, tapi bisa lebih panjang (scroll) di HP.
    2. flex-col: Default (HP) susun ke bawah.
    3. lg:flex-row: Layar Besar (Laptop) susun ke samping.
    4. lg:h-screen lg:overflow-hidden: Hanya di Laptop kunci tinggi layar (Kios Mode).
--}}

<body
    class="bg-slate-50 min-h-screen w-full flex flex-col lg:flex-row lg:h-screen lg:overflow-hidden selection:bg-blue-200 selection:text-blue-900">

    {{-- ================= KIRI: SIDEBAR INFORMASI ================= --}}
    {{-- 
         HP: w-full (Lebar Penuh), h-auto (Tinggi menyesuaikan isi).
         Laptop: w-3/12, h-full.
    --}}
    <div
        class="w-full lg:w-3/12 bg-gradient-to-br from-blue-700 to-blue-900 text-white p-6 lg:p-4 flex flex-col justify-between relative overflow-hidden shadow-2xl z-20 shrink-0">

        {{-- Hiasan Background --}}
        <div
            class="absolute top-0 right-0 -mr-20 -mt-20 w-60 h-60 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
        </div>
        <div
            class="absolute bottom-0 left-0 -ml-20 -mb-20 w-60 h-60 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
        </div>

        <div class="relative z-10 flex flex-col h-full gap-6 lg:gap-0">

            {{-- HEADER ATAS (LOGO & JUDUL) --}}
            <div class="text-center mb-0 lg:mb-3 shrink-0">
                <div class="mb-2 flex justify-center">
                    <img src="{{ asset('images/logo_pemda.png') }}" alt="Logo Pemda"
                        class="w-16 lg:w-16 h-auto drop-shadow-lg hover:scale-105 transition-transform duration-500">
                </div>

                <h1 class="text-xl lg:text-xl font-extrabold tracking-tight leading-none">KIOS ANTRIAN</h1>
                <p class="text-blue-200 text-[10px] mt-0.5 font-medium tracking-wider">MPP DELI SERDANG</p>

                {{-- JAM DIGITAL --}}
                <div
                    class="mt-4 lg:mt-3 bg-white/10 backdrop-blur-md border border-white/10 rounded-xl p-3 lg:p-2 shadow-lg">
                    <p class="text-[9px] text-blue-200 font-bold tracking-[0.2em] uppercase mb-0">WAKTU SAAT INI</p>
                    <div
                        class="flex justify-center items-baseline gap-1 text-3xl font-black tracking-tighter drop-shadow-sm">
                        <span id="jam-jam">00</span>
                        <span class="blink text-blue-300">:</span>
                        <span id="jam-menit">00</span>
                        <span class="text-base text-blue-300 font-bold ml-1" id="jam-detik">00</span>
                    </div>
                    <p id="jam-tanggal"
                        class="text-[10px] font-medium text-white/90 mt-0 border-t border-white/10 pt-1">
                        Senin, 1 Januari 2024
                    </p>
                </div>
            </div>

            <div class="flex-1 flex flex-col justify-center gap-4 lg:gap-3">

                {{-- Card Jam Operasional --}}
                <div
                    class="bg-white/10 backdrop-blur-md border border-white/10 rounded-xl p-4 lg:p-3 space-y-2 shadow-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center shrink-0">
                            <i class="fa fa-clock text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-blue-200 uppercase font-bold tracking-wider">Jam Operasional</p>
                            <p class="font-bold text-sm">08.00 – 15.00 WIB</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center shrink-0">
                            <i class="fa fa-calendar-days text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-blue-200 uppercase font-bold tracking-wider">Hari Kerja</p>
                            <p class="font-bold text-sm">Senin – Jumat</p>
                        </div>
                    </div>
                </div>

                {{-- Card MONITOR PANGGILAN --}}
                <div id="notifikasiPanggilan"
                    class="bg-gradient-to-br from-yellow-400 to-yellow-500 text-gray-900 rounded-xl p-4 shadow-xl border-4 border-white/20 flex flex-col items-center text-center relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-1 bg-white/40"></div>
                    <i
                        class="fa fa-bullhorn absolute -right-4 -bottom-4 text-6xl text-yellow-600 opacity-10 transform -rotate-12 group-hover:scale-110 transition-transform duration-700"></i>

                    <div
                        class="bg-white text-yellow-600 rounded-lg w-8 h-8 flex items-center justify-center mb-1 shadow-lg z-10 ring-2 ring-yellow-300/50">
                        <i class="fa fa-volume-high text-sm animate-pulse"></i>
                    </div>

                    <p class="text-[8px] font-bold uppercase tracking-[0.2em] mb-0 opacity-70 z-10 text-gray-800">
                        PANGGILAN TERAKHIR</p>

                    <h2 id="panggilanNo"
                        class="text-5xl font-black tracking-tighter my-0 z-10 text-gray-900 drop-shadow-sm">---</h2>

                    <p id="panggilanSkpd"
                        class="text-[10px] font-extrabold text-gray-800 mb-1 z-10 uppercase leading-tight line-clamp-2 px-1">
                        ---</p>

                    <div
                        class="bg-gray-900/10 backdrop-blur-sm px-3 py-1 rounded-full border border-gray-900/5 z-10 w-full">
                        <span id="panggilanLoket"
                            class="text-[10px] font-bold text-gray-900 uppercase block truncate">Menunggu...</span>
                    </div>
                </div>
            </div>

            <div class="text-center text-[9px] text-blue-300/60 relative z-10 mt-4 lg:mt-2 shrink-0">
                <p>&copy; {{ date('Y') }} MPP Deli Serdang</p>
            </div>
        </div>
    </div>

    {{-- ================= KANAN: MENU UTAMA ================= --}}
    {{-- 
         HP: w-full (Lebar Penuh).
         Laptop: w-9/12, overflow-y-auto (Scroll area kanan saja).
    --}}
    <div class="w-full lg:w-9/12 p-4 lg:p-8 relative bg-slate-50 lg:overflow-y-auto scroll-smooth">

        <div class="flex justify-between items-center mb-6 sticky top-0 bg-slate-50/90 backdrop-blur-sm z-20 py-2">
            <div>
                <h2 class="text-xl lg:text-2xl font-bold text-slate-800">Daftar Layanan</h2>
                <p class="text-xs lg:text-sm text-slate-500">Pilih instansi yang ingin Anda tuju</p>
            </div>

            <div class="flex items-center gap-3">
                <button
                    onclick="document.getElementById('modalLokasi').classList.remove('hidden'); document.getElementById('modalLokasi').classList.add('flex');"
                    class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm border border-blue-200"
                    title="Info Lokasi Stand">
                    <i class="fa fa-question text-sm font-bold"></i>
                </button>

                <div
                    class="bg-white px-3 py-1.5 rounded-full shadow-sm border border-slate-200 flex items-center gap-2 text-xs font-medium text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="hidden sm:inline">Sistem Online</span>
                    <span class="sm:hidden">Online</span>
                </div>
            </div>
        </div>

        {{-- Grid: 1 Kolom di HP, 2 di Tablet, 3-4 di Laptop --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 pb-10">
            @foreach ($skpd as $item)
                <button
                    class="group bg-white rounded-3xl p-5 text-center border border-slate-100 shadow-[0_2px_10px_-5px_rgba(0,0,0,0.05)] hover:shadow-[0_20px_40px_-15px_rgba(37,99,235,0.15)] hover:-translate-y-1 transition-all duration-300 flex flex-col items-center justify-center h-full min-h-[140px] lg:min-h-[160px] relative overflow-hidden"
                    onclick="openLayanan('{{ $item->id }}')">
                    <div
                        class="absolute inset-0 bg-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-3xl">
                    </div>
                    <div class="relative z-10 flex flex-col items-center h-full justify-center">
                        <div
                            class="w-14 h-14 lg:w-16 lg:h-16 mx-auto bg-blue-50 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:rotate-6 transition-all duration-300 shadow-inner group-hover:shadow-blue-300/50">
                            @if ($item->logo_skpd)
                                {{-- <img src="{{ asset('storage/' . $item->logo_skpd) }}" class="w-10 h-10 object-contain"> --}}
                                <i
                                    class="fa fa-building-columns text-2xl lg:text-3xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                            @else
                                <i
                                    class="fa fa-building text-2xl lg:text-3xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                            @endif
                        </div>
                        <p
                            class="font-bold text-slate-700 text-sm md:text-base leading-snug group-hover:text-blue-700 transition-colors line-clamp-3">
                            {{ $item->nama_skpd }}
                        </p>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ================= MODAL, AUDIO, SCRIPT (TIDAK ADA PERUBAHAN) ================= --}}

    {{-- ... (BAGIAN MODAL COPY PASTE DARI YANG LAMA SAJA KARENA SAMA) ... --}}

    <div id="modalLayanan"
        class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-all duration-300 p-4">
        <div
            class="bg-white rounded-[2rem] w-full max-w-xl shadow-2xl overflow-hidden transform scale-100 animate-in fade-in zoom-in duration-200 flex flex-col max-h-[90vh]">
            <div
                class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-5 shrink-0 flex items-center shadow-lg relative overflow-hidden">
                <i class="fa fa-building absolute -right-6 -bottom-6 text-8xl text-white opacity-10"></i>
                <button onclick="closeModal()"
                    class="mr-4 hover:bg-white/20 w-8 h-8 flex items-center justify-center rounded-full transition backdrop-blur-sm">
                    <i class="fa fa-arrow-left"></i>
                </button>
                <div>
                    <p class="text-blue-100 text-[10px] font-bold uppercase tracking-wider mb-0.5">Daftar Layanan</p>
                    <h2 id="judulSkpd" class="text-xl font-bold leading-none truncate pr-4">Pilih Layanan</h2>
                </div>
            </div>
            <div id="daftarLayanan" class="p-6 space-y-3 overflow-y-auto bg-slate-50"></div>
        </div>
    </div>

    <div id="modalForm"
        class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-all duration-300 p-4">
        <div
            class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-5 flex items-center shadow-lg relative">
                <button onclick="backToLayanan()"
                    class="mr-4 hover:bg-white/20 w-8 h-8 flex items-center justify-center rounded-full transition backdrop-blur-sm">
                    <i class="fa fa-arrow-left"></i>
                </button>
                <div>
                    <p class="text-blue-100 text-[10px] font-bold uppercase tracking-wider mb-0.5">Lengkapi Data</p>
                    <h2 id="judulLayanan" class="text-xl font-bold leading-none">Isi Data Diri</h2>
                </div>
            </div>
            <div class="p-6 bg-slate-50">
                <form method="POST" action="{{ route('ambil.antrian') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="skpd_id" id="inputSkpd">
                    <input type="hidden" name="loket_id" id="inputLoket">

                    <div class="space-y-1">
                        <label class="block text-slate-600 text-xs font-bold uppercase tracking-wide ml-1">NIK</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i
                                    class="fa fa-address-card"></i></span>
                            <input type="number" name="nik" required
                                class="w-full bg-white border border-slate-200 text-slate-800 font-semibold p-3 pl-10 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition shadow-sm placeholder-slate-300 text-sm"
                                placeholder="Masukkan 16 digit NIK">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-slate-600 text-xs font-bold uppercase tracking-wide ml-1">Nama
                            Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i
                                    class="fa fa-user"></i></span>
                            <input type="text" name="nama" required
                                class="w-full bg-white border border-slate-200 text-slate-800 font-semibold p-3 pl-10 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition shadow-sm placeholder-slate-300 text-sm"
                                placeholder="Nama sesuai KTP">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-slate-600 text-xs font-bold uppercase tracking-wide ml-1">No. HP /
                            WA</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i
                                    class="fa fa-whatsapp"></i></span>
                            <input type="number" name="no_hp" required
                                class="w-full bg-white border border-slate-200 text-slate-800 font-semibold p-3 pl-10 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition shadow-sm placeholder-slate-300 text-sm"
                                placeholder="Contoh: 08123456789">
                        </div>
                    </div>

                    <button
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-4 rounded-xl font-bold text-base shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2 mt-2">
                        <i class="fa fa-print"></i> CETAK NOMOR ANTRIAN
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="modalLokasi"
        class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-50 backdrop-blur-sm transition-all duration-300 p-4">
        <div
            class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div
                class="bg-gradient-to-r from-slate-700 to-slate-800 text-white p-5 flex items-center shadow-lg relative">
                <button
                    onclick="document.getElementById('modalLokasi').classList.add('hidden'); document.getElementById('modalLokasi').classList.remove('flex');"
                    class="mr-4 hover:bg-white/20 w-8 h-8 flex items-center justify-center rounded-full transition backdrop-blur-sm">
                    <i class="fa fa-times"></i>
                </button>
                <div>
                    <p class="text-slate-300 text-[10px] font-bold uppercase tracking-wider mb-0.5">Informasi Area</p>
                    <h2 class="text-xl font-bold leading-none">Lokasi Stand Layanan</h2>
                </div>
            </div>
            <div class="p-6 bg-slate-50 space-y-4">

                {{-- Item Lokasi 1 --}}
                <div class="bg-white border border-slate-200 p-4 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div
                        class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                        <i class="fa fa-building-user"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Polres & Samsat</h4>
                        <p class="text-sm text-slate-500 flex items-center gap-2"><i
                                class="fa fa-arrow-left text-xs"></i> Sebelah Kiri Gedung</p>
                    </div>
                </div>

                {{-- Item Lokasi 2 --}}
                <div class="bg-white border border-slate-200 p-4 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div
                        class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                        <i class="fa fa-network-wired"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Dinas Kominfostan</h4>
                        <p class="text-sm text-slate-500 flex items-center gap-2">Sebelah Kanan Gedung <i
                                class="fa fa-arrow-right text-xs"></i></p>
                    </div>
                </div>

                {{-- Item Lokasi 3 --}}
                <div class="bg-white border border-slate-200 p-4 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div
                        class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl shrink-0">
                        <i class="fa fa-mug-hot"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">Kantin & Toilet</h4>
                        <p class="text-sm text-slate-500">Lantai 1 Belakang</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if (session('tiket'))
        <div
            class="fixed inset-0 bg-slate-900/80 flex items-center justify-center z-[60] backdrop-blur-md animate-in fade-in duration-300 p-4">
            <div
                class="bg-white p-8 rounded-[2rem] text-center shadow-2xl transform scale-100 max-w-sm w-full relative overflow-hidden border-4 border-white/50">
                <div
                    class="absolute inset-0 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:16px_16px] opacity-10 pointer-events-none">
                </div>
                <div
                    class="mb-4 inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full text-green-500 animate-bounce relative z-10">
                    <i class="fa fa-check text-4xl"></i>
                </div>
                <p class="text-slate-400 font-bold text-xs tracking-widest uppercase mb-1 relative z-10">BERHASIL
                    DICETAK</p>
                <p class="text-slate-600 font-medium text-base relative z-10">Nomor Antrian Anda:</p>
                <h1
                    class="text-6xl font-black text-blue-600 my-4 tracking-tighter drop-shadow-sm bg-blue-50 py-3 rounded-2xl border border-blue-100 relative z-10">
                    {{ session('tiket') }}
                </h1>
                <p class="text-slate-500 mb-6 px-2 text-xs leading-relaxed relative z-10">Silakan duduk dan menunggu
                    nomor Anda dipanggil.</p>
                <a href="/"
                    class="block w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition shadow-lg text-sm relative z-20">SELESAI</a>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="fixed inset-0 bg-slate-900/80 flex items-center justify-center z-[60] backdrop-blur-sm"
            id="errorModal">
            <div
                class="bg-white p-6 rounded-[2rem] text-center max-w-sm w-full mx-4 shadow-2xl border-t-8 border-red-500">
                <div
                    class="w-14 h-14 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa fa-exclamation-triangle text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mb-2">Validasi Gagal</h2>
                <div class="bg-red-50 border border-red-100 rounded-xl p-3 mb-4 text-left">
                    <ul class="list-disc list-inside text-red-600 text-xs font-medium space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="document.getElementById('errorModal').style.display='none'"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition shadow-lg hover:shadow-red-500/30 text-sm">
                    TUTUP
                </button>
            </div>
        </div>
    @endif

    <audio id="tingtung" src="{{ asset('assets/audio/tingtung.mp3') }}"></audio>

    <script>
        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];

            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');

            document.getElementById('jam-jam').innerText = h;
            document.getElementById('jam-menit').innerText = m;
            document.getElementById('jam-detik').innerText = s;

            const dayName = days[now.getDay()];
            const dateNum = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();

            document.getElementById('jam-tanggal').innerText = `${dayName}, ${dateNum} ${monthName} ${year}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        const skpdData = @json($skpd);

        function openLayanan(skpdId) {
            const skpd = skpdData.find(s => s.id == skpdId);
            document.getElementById('judulSkpd').innerText = skpd.nama_skpd;
            const container = document.getElementById('daftarLayanan');
            container.innerHTML = '';

            if (skpd.lokets.length === 0) {
                container.innerHTML =
                    '<div class="text-center py-10 text-slate-400 flex flex-col items-center"><div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-3"><i class="fa fa-ban text-2xl"></i></div><p class="font-medium text-sm">Belum ada layanan aktif.</p></div>';
            }

            skpd.lokets.forEach(loket => {
                const btn = document.createElement('button');
                btn.className =
                    'w-full bg-white border border-slate-200 p-4 rounded-xl flex justify-between items-center hover:bg-blue-50 hover:border-blue-300 transition-all shadow-sm hover:shadow-md mb-3 group text-left';
                btn.innerHTML =
                    `<span class="font-bold text-slate-700 group-hover:text-blue-700 text-sm transition-colors">${loket.nama_loket}</span><span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center group-hover:bg-blue-200 group-hover:text-blue-700 transition-all"><i class="fa fa-chevron-right text-xs text-slate-400 group-hover:text-blue-700"></i></span>`;
                btn.onclick = () => openForm(skpd.id, loket.id, loket.nama_loket);
                container.appendChild(btn);
            });

            document.getElementById('modalLayanan').classList.remove('hidden');
            document.getElementById('modalLayanan').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('modalLayanan').classList.add('hidden');
            document.getElementById('modalLayanan').classList.remove('flex');
            document.getElementById('modalForm').classList.add('hidden');
            document.getElementById('modalForm').classList.remove('flex');
        }

        function openForm(skpdId, loketId, namaLayanan) {
            document.getElementById('modalLayanan').classList.add('hidden');
            document.getElementById('modalLayanan').classList.remove('flex');
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalForm').classList.add('flex');
            document.getElementById('judulLayanan').innerText = namaLayanan;
            document.getElementById('inputSkpd').value = skpdId;
            document.getElementById('inputLoket').value = loketId;
        }

        function backToLayanan() {
            document.getElementById('modalForm').classList.add('hidden');
            document.getElementById('modalForm').classList.remove('flex');
            document.getElementById('modalLayanan').classList.remove('hidden');
            document.getElementById('modalLayanan').classList.add('flex');
        }

        const bell = document.getElementById('tingtung');
        const notifElem = document.getElementById('notifikasiPanggilan');
        let speechQueue = [];
        let lastCallTime = null;

        const debugDiv = document.createElement('div');
        debugDiv.style.cssText =
            "position:fixed; bottom:0; right:0; padding:5px; font-size:10px; color:gray; opacity:0; pointer-events:none; transition:opacity 0.3s;";
        debugDiv.id = "debug-status";
        debugDiv.innerHTML = "Status: Menunggu...";
        document.body.appendChild(debugDiv);

        document.body.addEventListener('mousemove', (e) => {
            if (window.innerWidth - e.clientX < 50 && window.innerHeight - e.clientY < 50) {
                debugDiv.style.opacity = '1';
            } else {
                debugDiv.style.opacity = '0';
            }
        });

        async function cekServer() {
            try {
                document.getElementById('debug-status').innerText = "Cek: " + new Date().toLocaleTimeString();
                const response = await fetch("{{ route('antrian.check') }}");
                if (!response.ok) throw new Error('Network error');

                const data = await response.json();

                if (!data || !data.no_antrian) return;

                if (lastCallTime === null) {
                    lastCallTime = data.waktu_panggil;
                    console.log("Init Data:", data.no_antrian);
                    document.getElementById('panggilanNo').innerText = data.no_antrian;
                    document.getElementById('panggilanSkpd').innerText = data.nama_skpd;
                    document.getElementById('panggilanLoket').innerText = data.nama_loket;
                    return;
                }

                if (data.waktu_panggil !== lastCallTime) {
                    console.log("🔥 CALL BARU!", data);
                    lastCallTime = data.waktu_panggil;
                    tampilkanOverlay(data.no_antrian, data.nama_loket, data.nama_skpd);
                    putarAudio(data);
                }
            } catch (error) {
                console.error("Polling error:", error);
                document.getElementById('debug-status').innerText = "Error!";
            }
        }

        setInterval(cekServer, 3000);

        window.addEventListener('click', function(e) {
            const modalLayanan = document.getElementById('modalLayanan');
            const modalForm = document.getElementById('modalForm');
            const modalLokasi = document.getElementById('modalLokasi');

            if (e.target === modalLayanan) closeModal();
            if (e.target === modalForm) closeModal();
            if (e.target === modalLokasi) {
                modalLokasi.classList.add('hidden');
                modalLokasi.classList.remove('flex');
            }
        });

        function tampilkanOverlay(nomor, loket, skpd) {
            document.getElementById('panggilanNo').innerText = nomor;
            document.getElementById('panggilanLoket').innerText = loket;
            document.getElementById('panggilanSkpd').innerText = skpd;

            const card = document.getElementById('notifikasiPanggilan');
            card.classList.remove('animate-pulse', 'ring-4', 'ring-yellow-300');
            void card.offsetWidth;
            card.classList.add('animate-pulse', 'ring-4', 'ring-yellow-300');

            setTimeout(() => {
                card.classList.remove('animate-pulse', 'ring-4', 'ring-yellow-300');
            }, 5000);
        }

        function putarAudio(data) {
            speechSynthesis.cancel();
            speechQueue = [];

            bell.pause();
            bell.currentTime = 0;
            let playPromise = bell.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.warn("⚠️ Audio blocked. User interaction needed.");
                });
            }

            let kalimat = [
                `Nomor antrian. ${data.no_antrian}.`,
                `Silakan menuju. ${data.nama_skpd}.`
            ];

            kalimat.forEach(txt => speechQueue.push(txt));
            speechQueue.push("Jeda");
            kalimat.forEach(txt => speechQueue.push(txt));

            setTimeout(() => {
                processSpeechQueue();
            }, 1500);
        }

        function processSpeechQueue() {
            if (speechQueue.length === 0) return;
            let text = speechQueue.shift();

            if (text === "Jeda") {
                setTimeout(() => processSpeechQueue(), 1000);
                return;
            }

            let utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.85;
            utterance.pitch = 1;

            utterance.onend = () => {
                setTimeout(() => processSpeechQueue(), 200);
            };

            utterance.onerror = (e) => {
                processSpeechQueue();
            };

            speechSynthesis.speak(utterance);
        }

        document.body.addEventListener('click', () => {
            bell.play().then(() => {
                bell.pause();
                bell.currentTime = 0;
            }).catch(() => {});
        }, {
            once: true
        });
    </script>
</body>

</html>
