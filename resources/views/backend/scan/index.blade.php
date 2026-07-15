@extends('backend.layout.app')
@section('title', 'Scan Antrean')

@push('stylesheets')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        #reader { width: 100%; max-width: 420px; margin: 0 auto; border-radius: 14px; overflow: hidden; }
        #scanResult { display: none; }
        .scan-foto { width: 100%; max-width: 280px; aspect-ratio: 4/3; object-fit: cover; border-radius: 14px; background: #e2e8f0; }
        .scan-row { display: flex; gap: 8px; padding: 8px 0; border-bottom: 1px dashed #e3e8f0; }
        .scan-row .lbl { width: 130px; color: #64748b; font-size: 13px; flex: 0 0 auto; }
        .scan-row .val { font-weight: 600; font-size: 14px; }
    </style>
@endpush

@section('content')
    <div id="kt_app_toolbar" class="app-toolbar py-4">
        <div class="app-container container-fluid">
            <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Scan Antrean</h1>
            <ul class="breadcrumb fw-semibold fs-7 my-1">
                <li class="breadcrumb-item text-muted">Antrian Online</li>
                <li class="breadcrumb-item text-gray-900">Scan</li>
            </ul>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-fluid">
            <div class="row g-5">
                {{-- SCANNER --}}
                <div class="col-lg-5">
                    <div class="card border border-gray-300">
                        <div class="card-header"><h3 class="card-title fw-bold">Pindai QR Tiket</h3></div>
                        <div class="card-body">
                            <div id="reader"></div>
                            <div class="text-center text-muted fs-8 my-3" id="scanHint">Arahkan QR tiket ke kamera.</div>
                            <div class="d-flex gap-2 justify-content-center mb-5">
                                <button class="btn btn-sm btn-primary" id="btnStart"><i class="ki-outline ki-scan-barcode fs-4"></i> Mulai Pindai</button>
                                <button class="btn btn-sm btn-light" id="btnStop" style="display:none;">Stop</button>
                            </div>
                            <div class="separator mb-4"></div>
                            <label class="form-label fs-7">Atau masukkan nomor antrean / kode manual</label>
                            <label class="form-label fs-8 text-muted mt-2 mb-1">Tanggal Antrean</label>
                            <input type="date" id="manualTanggal" class="form-control form-control-sm mb-2">
                            <div class="input-group input-group-sm">
                                <input type="text" id="manualKode" class="form-control" placeholder="mis. AKHO-001">
                                <button class="btn btn-light-primary" id="btnCari">Cari</button>
                            </div>
                            <div class="form-text fs-8">Isi <b>tanggal</b> agar akurat — nomor antrean online berulang tiap hari untuk layanan yang sama. Via <b>pindai QR</b> selalu akurat (pakai ID unik).</div>
                        </div>
                    </div>
                </div>

                {{-- HASIL --}}
                <div class="col-lg-7">
                    <div class="card border border-gray-300" id="scanResult">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Detail Antrean</h3>
                            <div class="card-toolbar">
                                <span class="badge fs-7" id="r-sumber"></span>
                                <span class="badge badge-light-primary fs-7 ms-2" id="r-status"></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5 text-center mb-4">
                                    <img id="r-foto" class="scan-foto" alt="Foto Wajah">
                                    <div class="text-muted fs-8 mt-2" id="r-foto-note"></div>
                                </div>
                                <div class="col-md-7">
                                    <div class="text-center mb-4">
                                        <div class="text-muted fs-8">Nomor Antrean</div>
                                        <div class="fw-bolder text-primary" style="font-size:32px;" id="r-no">-</div>
                                        <div class="fw-bold" id="r-tanggal">-</div>
                                    </div>
                                    <div class="scan-row"><span class="lbl">Instansi</span><span class="val" id="r-skpd">-</span></div>
                                    <div class="scan-row"><span class="lbl">Layanan</span><span class="val" id="r-layanan">-</span></div>
                                    <div class="scan-row"><span class="lbl">NIK</span><span class="val" id="r-nik">-</span></div>
                                    <div class="scan-row"><span class="lbl">Nama</span><span class="val" id="r-nama">-</span></div>
                                    <div class="scan-row"><span class="lbl">Jenis Kelamin</span><span class="val" id="r-jk">-</span></div>
                                    <div class="scan-row"><span class="lbl">No. HP/WA</span><span class="val" id="r-hp">-</span></div>
                                </div>
                            </div>
                            <div id="r-forms"></div>
                        </div>
                    </div>
                    <div class="card border border-gray-300" id="scanEmpty">
                        <div class="card-body text-center text-muted py-10">
                            <i class="ki-outline ki-scan-barcode fs-3x mb-3 d-block"></i>
                            Hasil scan akan muncul di sini.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('vendor/html5-qrcode/html5-qrcode.min.js') }}"></script>
        <script>
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const LOOKUP = "{{ route('scan.lookup') }}";
            const DEFAULT_FOTO = "{{ asset('assets/media/logos/logo_deliserdang.png') }}";
            let html5Qr = null, scanning = false;
            (function () { const d = document.getElementById('manualTanggal'); if (d) { const t = new Date(); d.value = t.getFullYear() + '-' + String(t.getMonth() + 1).padStart(2, '0') + '-' + String(t.getDate()).padStart(2, '0'); } })();

            function setBadgeSumber(s) {
                const el = document.getElementById('r-sumber');
                if (s === 'online') { el.textContent = 'Online'; el.className = 'badge badge-light-info fs-7'; }
                else { el.textContent = 'Loket / Kiosk'; el.className = 'badge badge-light-dark fs-7'; }
            }

            function render(d) {
                document.getElementById('scanEmpty').style.display = 'none';
                document.getElementById('scanResult').style.display = 'block';
                document.getElementById('r-no').textContent = d.no_antrian;
                document.getElementById('r-tanggal').textContent = d.tanggal;
                document.getElementById('r-status').textContent = d.status;
                setBadgeSumber(d.sumber);
                document.getElementById('r-skpd').textContent = d.skpd;
                document.getElementById('r-layanan').textContent = d.layanan;
                document.getElementById('r-nik').textContent = d.nik;
                document.getElementById('r-nama').textContent = d.nama;
                document.getElementById('r-jk').textContent = d.jk;
                document.getElementById('r-hp').textContent = d.no_hp;
                const foto = document.getElementById('r-foto');
                const note = document.getElementById('r-foto-note');
                if (d.foto) {
                    foto.onerror = function () { this.onerror = null; this.src = DEFAULT_FOTO; note.textContent = 'Foto gagal dimuat (pastikan storage:link aktif).'; };
                    foto.src = d.foto;
                    note.textContent = '';
                } else {
                    foto.src = DEFAULT_FOTO;
                    note.textContent = 'Foto sudah dibersihkan / tidak tersedia.';
                }
                renderForms(d.forms || []);
            }

            function esc(s) { return String(s == null ? '' : s).replace(/[&<>]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c])); }

            function renderForms(forms) {
                const host = document.getElementById('r-forms');
                if (!host) return;
                if (!forms || !forms.length) { host.innerHTML = ''; return; }
                let html = '<div class="separator my-4"></div><h4 class="fw-bold fs-6 mb-3"><i class="ki-outline ki-document fs-4 me-1"></i>Form Persyaratan</h4>';
                forms.forEach(function (f) {
                    html += '<div class="border border-gray-300 rounded p-4 mb-3">';
                    html += '<div class="fw-bold text-primary mb-2">' + esc(f.nama) + ' <span class="text-muted fs-8">(' + esc(f.kode) + ')</span></div>';
                    if (!f.sections || !f.sections.length) { html += '<div class="text-muted fs-8">Tidak ada isian.</div></div>'; return; }
                    f.sections.forEach(function (sec) {
                        if (sec.title) html += '<div class="fw-bold text-muted text-uppercase fs-8 mt-3 mb-1">' + esc(sec.title) + '</div>';
                        sec.items.forEach(function (it) {
                            const v = it.fileUrl
                                ? '<a href="' + it.fileUrl + '" target="_blank" class="text-primary fw-bold">' + esc(it.value) + ' &#8599;</a>'
                                : esc(it.value).replace(/\n/g, '<br>');
                            html += '<div class="scan-row"><span class="lbl">' + esc(it.label) + '</span><span class="val">' + v + '</span></div>';
                        });
                    });
                    html += '</div>';
                });
                host.innerHTML = html;
            }

            function lookup(kode) {
                kode = (kode || '').trim();
                if (!kode) { Swal.fire('Kosong', 'Masukkan nomor antrean / pindai QR dulu.', 'info'); return; }

                Swal.fire({ title: 'Mencari...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                const tgl = (document.getElementById('manualTanggal') || {}).value || '';
                fetch(LOOKUP, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
                    body: 'kode=' + encodeURIComponent(kode) + '&tanggal=' + encodeURIComponent(tgl)
                })
                .then(async function (r) {
                    let j = null;
                    try { j = await r.json(); } catch (e) {}
                    return { status: r.status, j: j };
                })
                .then(function (res) {
                    if (res.j && res.j.success) {
                        render(res.j);
                        Swal.fire({ icon: 'success', title: 'Ditemukan', text: res.j.message || ('Nomor ' + res.j.no_antrian), timer: 1600, showConfirmButton: false });
                    } else {
                        const msg = (res.j && res.j.message) ? res.j.message : ('Gagal memuat data (HTTP ' + res.status + ').');
                        Swal.fire({ icon: res.status === 404 ? 'warning' : 'error', title: res.status === 404 ? 'Tidak Ditemukan' : 'Gagal', text: msg });
                    }
                })
                .catch(function () {
                    Swal.fire('Error', 'Tidak dapat menghubungi server. Coba lagi.', 'error');
                });
            }

            function stopScan() {
                if (html5Qr && scanning) {
                    html5Qr.stop().then(() => { scanning = false; }).catch(() => {});
                }
                document.getElementById('btnStart').style.display = '';
                document.getElementById('btnStop').style.display = 'none';
            }

            document.getElementById('btnStart').addEventListener('click', function () {
                if (typeof Html5Qrcode === 'undefined') { document.getElementById('scanHint').textContent = 'Library scanner gagal dimuat.'; return; }
                html5Qr = html5Qr || new Html5Qrcode('reader');
                const scanConfig = {
                    fps: 15,
                    qrbox: function (vw, vh) { var m = Math.floor(Math.min(vw, vh) * 0.8); return { width: m, height: m }; },
                    experimentalFeatures: { useBarCodeDetectorIfSupported: true }
                };
                html5Qr.start({ facingMode: 'environment' }, scanConfig,
                    function (decodedText) { stopScan(); lookup(decodedText); },
                    function () {}
                ).then(() => {
                    scanning = true;
                    document.getElementById('btnStart').style.display = 'none';
                    document.getElementById('btnStop').style.display = '';
                    document.getElementById('scanHint').textContent = 'Arahkan QR tiket ke kamera...';
                }).catch(function (e) {
                    document.getElementById('scanHint').textContent = 'Tidak bisa akses kamera. Pakai input manual di bawah. (' + e + ')';
                });
            });
            document.getElementById('btnStop').addEventListener('click', stopScan);
            document.getElementById('btnCari').addEventListener('click', () => lookup(document.getElementById('manualKode').value.trim()));
            document.getElementById('manualKode').addEventListener('keyup', function (e) { if (e.key === 'Enter') lookup(this.value.trim()); });
        </script>
    @endpush
@endsection
