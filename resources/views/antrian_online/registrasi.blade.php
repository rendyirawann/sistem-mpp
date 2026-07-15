@extends('antrian_online.layout')
@section('title', 'Registrasi Antrean Online')

@push('styles')
<style>
    .rw-wrap { padding-top: 110px; padding-bottom: 50px; }
    .rw-container { width: 100%; max-width: 1100px; margin: 0 auto; padding: 0 clamp(16px, 4vw, 32px); }

    /* Stepper */
    .rw-steps { display: flex; align-items: flex-start; justify-content: space-between; margin: 0 auto 30px; max-width: 720px; }
    .rw-step { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
    .rw-step:not(:last-child)::after {
        content: ""; position: absolute; top: 18px; left: 50%; width: 100%; height: 3px;
        background: #e5e7eb; z-index: 0;
    }
    .rw-step.done:not(:last-child)::after { background: var(--ao-primary); }
    .rw-dot {
        width: 38px; height: 38px; border-radius: 50%; background: #e5e7eb; color: #fff;
        display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px;
        z-index: 1; transition: background .25s;
    }
    .rw-step.active .rw-dot, .rw-step.done .rw-dot { background: var(--ao-primary); }
    .rw-step-label { font-size: 12px; font-weight: 600; color: var(--ao-muted); margin-top: 8px; text-align: center; }
    .rw-step.active .rw-step-label, .rw-step.done .rw-step-label { color: var(--ao-primary-dark); }

    .rw-grid { display: grid; grid-template-columns: 1.9fr 1fr; gap: 22px; align-items: start; }

    .rw-card { background: #fff; border-radius: 28px; box-shadow: var(--ao-shadow); overflow: hidden; border: 1px solid var(--ao-border); }
    .rw-card-head { background: #fff; color: var(--ao-text); padding: 22px 26px 16px; border-bottom: 1px solid var(--ao-border); }
    .rw-card-head h3 { margin: 0; font-size: 18px; font-weight: 700; letter-spacing: -0.4px; }
    .rw-card-head p { margin: 4px 0 0; font-size: 13px; color: var(--ao-muted); }
    .rw-card-body { padding: 26px; }

    .rw-field { margin-bottom: 18px; }
    .rw-field label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 7px; }
    .rw-input { width: 100%; padding: 13px 16px; border: 2px solid transparent; background: #f8f9fc; border-radius: 16px; font-size: 14px; font-family: inherit; outline: none; transition: border-color .15s, box-shadow .15s; }
    .rw-input:focus { border-color: var(--ao-primary); background: #fff; box-shadow: 0 0 0 4px var(--ao-accent-light); }
    .rw-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .rw-jk { display: flex; gap: 12px; }
    .rw-jk label { flex: 1; display: flex; align-items: center; gap: 8px; padding: 11px 14px; border: 1px solid var(--ao-border); border-radius: 12px; cursor: pointer; font-size: 14px; margin: 0; }
    .rw-jk input { accent-color: var(--ao-primary); }
    .rw-jk label.sel { border-color: var(--ao-primary); background: rgba(79,70,229,.06); }
    .rw-err { color: #dc2626; font-size: 12px; margin-top: 5px; display: none; }

    /* Day picker */
    .rw-days { display: grid; grid-template-columns: repeat(auto-fill, minmax(108px, 1fr)); gap: 10px; }
    .rw-day { display: flex; flex-direction: column; align-items: center; gap: 2px; padding: 12px 8px; border: 1.5px solid var(--ao-border); border-radius: 16px; background: #fff; cursor: pointer; transition: .15s; font-family: inherit; }
    .rw-day:hover:not(:disabled) { border-color: var(--ao-primary); transform: translateY(-2px); }
    .rw-day.sel { border-color: var(--ao-primary); background: rgba(79, 70, 229, .07); }
    .rw-day-name { font-weight: 700; font-size: 13px; color: var(--ao-text); }
    .rw-day-date { font-size: 12px; color: var(--ao-muted); }
    .rw-day-sisa { font-size: 11px; font-weight: 700; color: #16a34a; margin-top: 2px; }
    .rw-day-full { opacity: .5; cursor: not-allowed; }
    .rw-day-full .rw-day-sisa { color: #dc2626; }

    .rw-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 26px; gap: 10px; }
    .rw-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 26px; border-radius: 100px; font-weight: 700; font-size: 14px; border: none; cursor: pointer; transition: .15s; font-family: inherit; }
    .rw-btn-primary { background: var(--ao-primary); color: #fff; box-shadow: 0 10px 24px -10px rgba(79, 70, 229, .5); }
    .rw-btn-primary:hover { background: var(--ao-primary-dark); transform: scale(1.02); }
    .rw-btn-primary:disabled { opacity: .5; cursor: not-allowed; }
    .rw-btn-light { background: #f3f4f6; color: #475569; }

    /* Camera */
    .rw-cam { width: 100%; aspect-ratio: 4/3; background: #cbd5e1; border-radius: 16px; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center; }
    .rw-cam video, .rw-cam img { width: 100%; height: 100%; object-fit: cover; }
    .rw-cam-frame { position: absolute; inset: 12%; border: 3px dashed rgba(255,255,255,.7); border-radius: 16px; pointer-events: none; }
    .rw-cam-hint { text-align: center; font-size: 13px; color: var(--ao-muted); margin-top: 12px; min-height: 18px; }
    .rw-cam-btns { display: flex; gap: 10px; justify-content: center; margin-top: 14px; }

    /* Side info */
    .rw-side-card { background: #fff; border: 1px solid var(--ao-border); border-radius: 18px; padding: 18px 20px; margin-bottom: 16px; display: flex; gap: 12px; }
    .rw-side-ic { flex: 0 0 auto; width: 40px; height: 40px; border-radius: 12px; background: #e0e7ff; color: var(--ao-primary); display: flex; align-items: center; justify-content: center; }
    .rw-side-card h4 { margin: 0 0 4px; font-size: 14px; font-weight: 700; }
    .rw-side-card p { margin: 0; font-size: 12.5px; color: var(--ao-muted); }
    .rw-note { background: #eef2ff; border-left: 4px solid var(--ao-primary); border-radius: 8px; padding: 12px 14px; font-size: 12.5px; color: #334155; }

    /* Review */
    .rw-review-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 24px; }
    .rw-rv-label { font-size: 11.5px; color: var(--ao-muted); }
    .rw-rv-val { font-size: 14px; font-weight: 600; color: var(--ao-text); margin-bottom: 12px; }
    .rw-permohonan { display: flex; align-items: center; gap: 12px; border: 1px solid var(--ao-border); border-radius: 14px; padding: 14px; margin-top: 6px; }
    .rw-permohonan .ic { width: 44px; height: 44px; border-radius: 14px; background: var(--ao-primary); color: #fff; display: flex; align-items: center; justify-content: center; flex: 0 0 auto; }
    .rw-rv-photo { width: 100%; aspect-ratio: 4/3; border-radius: 14px; object-fit: cover; background: #cbd5e1; }
    .rw-agree { display: flex; gap: 10px; align-items: flex-start; background: #eef2ff; border-left: 4px solid var(--ao-primary); border-radius: 8px; padding: 14px; margin-top: 20px; font-size: 12.5px; color: #334155; }
    .rw-agree input { margin-top: 3px; accent-color: var(--ao-primary); width: 16px; height: 16px; }

    /* Ticket */
    .rw-success { text-align: center; }
    .rw-success-ic { width: 64px; height: 64px; border-radius: 50%; background: var(--ao-primary); color: #fff; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px; box-shadow: 0 14px 30px -10px rgba(79, 70, 229, .5); }
    .rw-ticket { max-width: 420px; margin: 18px auto 0; background: #fff; border: 1px solid var(--ao-border); border-radius: 24px; overflow: hidden; box-shadow: var(--ao-shadow); text-align: left; }
    .rw-ticket-head { background: var(--ao-navy); color: #fff; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; }
    .rw-ticket-body { padding: 20px; }
    .rw-no { font-size: 34px; font-weight: 800; color: var(--ao-primary-dark); }
    .rw-ticket-table { width: 100%; font-size: 13px; background: #f8f9fc; border-radius: 10px; padding: 12px; margin: 14px 0; }
    .rw-ticket-table td { padding: 4px 6px; vertical-align: top; }
    .rw-qr { display: flex; justify-content: center; padding: 10px 0; }
    .rw-qr canvas, .rw-qr img { border-radius: 8px; }

    .rw-step-panel { display: none; }
    .rw-step-panel.active { display: block; }

    /* Modal */
    .rw-modal { position: fixed; inset: 0; z-index: 1500; display: none; align-items: center; justify-content: center; background: rgba(15,23,42,.55); padding: 20px; }
    .rw-modal.open { display: flex; }
    .rw-modal-box { background: #fff; border-radius: 28px; padding: 30px; max-width: 380px; width: 100%; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .25); }

    .rw-full-notice { background: #fff; border: 1px solid var(--ao-border); border-radius: 32px; padding: 44px 28px; text-align: center; box-shadow: var(--ao-shadow); max-width: 560px; margin: 0 auto; }

    @media (max-width: 820px) {
        .rw-grid { grid-template-columns: 1fr; }
        .rw-review-grid { grid-template-columns: 1fr; }
        .rw-step-label { font-size: 10px; }
    }
    @media (max-width: 520px) {
        .rw-row2 { grid-template-columns: 1fr; }
        .rw-jk { flex-direction: column; }
        .rw-card-body { padding: 20px 16px; }
    }
    /* Form persyaratan (dinamis) */
    .rw-formprog { font-size: 12px; font-weight: 700; color: var(--ao-primary); margin-bottom: 14px; }
    .rw-ffield { margin-bottom: 16px; }
    .rw-optgrid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px; }
    .rw-optitem { display: flex; align-items: center; gap: 8px; padding: 9px 12px; border: 1px solid var(--ao-border); border-radius: 10px; cursor: pointer; font-size: 13px; }
    .rw-optitem input { accent-color: var(--ao-primary); flex: 0 0 auto; }
    .rw-repeater-row { display: flex; gap: 8px; align-items: flex-end; border: 1px solid var(--ao-border); border-radius: 12px; padding: 12px; margin-bottom: 8px; flex-wrap: wrap; }
    .rw-repeater-cell { flex: 1 1 140px; min-width: 120px; }
    .rw-repeater-lbl { font-size: 11px; color: var(--ao-muted); margin-bottom: 4px; }
    .rw-repeater-del { flex: 0 0 auto; width: 34px; height: 40px; border: 1px solid #fecaca; background: #fef2f2; color: #dc2626; border-radius: 10px; cursor: pointer; font-size: 18px; line-height: 1; }
    .rw-fsummary { margin-top: 6px; }
    .rw-fsum-form { border: 1px solid var(--ao-border); border-radius: 12px; padding: 14px; margin-bottom: 12px; }
    .rw-fsum-form h5 { margin: 0 0 8px; font-size: 13px; font-weight: 700; color: var(--ao-primary-dark); }
    .rw-fsum-sec { font-size: 11.5px; font-weight: 700; color: var(--ao-muted); margin: 8px 0 3px; text-transform: uppercase; letter-spacing: .3px; }
    .rw-fsum-row { display: flex; gap: 10px; font-size: 12.5px; padding: 3px 0; border-bottom: 1px dashed var(--ao-border); }
    .rw-fsum-row .k { color: var(--ao-muted); flex: 0 0 42%; }
    .rw-fsum-row .v { font-weight: 600; word-break: break-word; }

    /* Form persyaratan inline di Step 1 */
    .rw-forminline { margin-top: 4px; }
    .rw-form-head { border-top: 1px dashed var(--ao-border); margin-top: 8px; padding-top: 18px; margin-bottom: 4px; }
    .rw-form-headtitle { font-size: 15px; font-weight: 800; color: var(--ao-primary-dark); }
    .rw-form-headsub { font-size: 12.5px; color: var(--ao-muted); margin-top: 2px; }
    .rw-form-formtitle { font-size: 13px; font-weight: 700; color: var(--ao-primary); margin: 16px 0 6px; }
    .rw-form-section { border: 1px solid var(--ao-border); border-radius: 12px; padding: 14px 16px; margin-bottom: 12px; }
    .rw-form-sectitle { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; color: var(--ao-muted); margin-bottom: 10px; }
    .rw-form-pager { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--ao-border); }
    .rw-page-info { font-size: 12px; font-weight: 700; color: var(--ao-muted); }
    .rw-wilayah-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .rw-wilayah-lbl { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px; }
    @media (max-width: 520px) { .rw-wilayah-grid { grid-template-columns: 1fr; } }

    @media print {
        body * { visibility: hidden; }
        #rw-ticket, #rw-ticket * { visibility: visible; }
        #rw-ticket { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; }
    }
</style>
@endpush

@section('content')
<div class="rw-wrap">
    <div class="rw-container">

        @if ($kuotaHabis)
            <div class="rw-full-notice">
                <div class="rw-success-ic" style="background:#fef3c7;color:#d97706;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                </div>
                @if (($noticeType ?? 'penuh') === 'jam')
                    <h2 style="margin:6px 0;font-size:20px;color:var(--ao-primary-dark);">Batas Jam Pengambilan Terlewati</h2>
                    <p style="color:var(--ao-muted);margin:0 0 18px;">Pengambilan antrean online untuk <b>{{ $skpd->nama_skpd }}</b> hari ini sudah melewati batas jam (pukul <b>{{ $cutoffLabel ?? '14:00' }}</b>). Silakan ambil antrean online lagi pada <b>hari kerja berikutnya</b>.</p>
                @else
                    <h2 style="margin:6px 0;font-size:20px;color:var(--ao-primary-dark);">Kuota Antrean Online Minggu Ini Penuh</h2>
                    <p style="color:var(--ao-muted);margin:0 0 18px;">Kuota antrean online untuk <b>{{ $skpd->nama_skpd }}</b> minggu ini sudah penuh. Silakan ambil antrean online lagi pada <b>Senin berikutnya</b>.</p>
                @endif
                <a href="{{ route('antrian-online') }}" class="rw-btn rw-btn-primary" style="text-decoration:none;">Kembali ke Beranda</a>
            </div>
        @else

        {{-- Stepper --}}
        <div class="rw-steps" id="rwSteps">
            <div class="rw-step active" data-step="1"><div class="rw-dot">1</div><div class="rw-step-label">Isi Data Diri</div></div>
            <div class="rw-step" data-step="2"><div class="rw-dot">2</div><div class="rw-step-label">Verifikasi Wajah</div></div>
            <div class="rw-step" data-step="3"><div class="rw-dot">3</div><div class="rw-step-label">Validasi Data</div></div>
            <div class="rw-step" data-step="4"><div class="rw-dot">4</div><div class="rw-step-label">Selesai</div></div>
        </div>

        {{-- STEP 1 --}}
        <div class="rw-step-panel active" id="panel-1">
            <div class="rw-grid">
                <div class="rw-card">
                    <div class="rw-card-head">
                        <h3>Informasi Data Pemohon</h3>
                        <p>Pastikan data yang Anda masukkan sesuai dengan E-KTP.</p>
                    </div>
                    <div class="rw-card-body">
                        <div class="rw-field">
                            <label>Pilih Hari Pelayanan (minggu ini)</label>
                            <div class="rw-days" id="rwDays">
                                @forelse ($hariTersedia as $h)
                                    <button type="button" class="rw-day {{ $h['penuh'] ? 'rw-day-full' : '' }}"
                                        data-tanggal="{{ $h['tanggal'] }}" {{ $h['penuh'] ? 'disabled' : '' }}>
                                        <span class="rw-day-name">{{ $h['hari'] }}</span>
                                        <span class="rw-day-date">{{ $h['tanggal_label'] }}</span>
                                        <span class="rw-day-sisa">{{ $h['penuh'] ? 'Penuh' : 'Sisa ' . $h['sisa'] }}</span>
                                    </button>
                                @empty
                                    <div class="text-muted" style="grid-column:1/-1;">Tidak ada hari pelayanan tersedia minggu ini.</div>
                                @endforelse
                            </div>
                            <div class="rw-err" id="e-tanggal"></div>
                        </div>
                        <div class="rw-row2">
                            <div class="rw-field">
                                <label>NIK</label>
                                <input type="text" id="f-nik" class="rw-input" inputmode="numeric" maxlength="16" placeholder="Masukkan 16 digit NIK">
                                <div class="rw-err" id="e-nik"></div>
                            </div>
                            <div class="rw-field">
                                <label>Nama</label>
                                <input type="text" id="f-nama" class="rw-input" placeholder="Sesuai KTP">
                                <div class="rw-err" id="e-nama"></div>
                            </div>
                        </div>
                        <div class="rw-row2">
                            <div class="rw-field">
                                <label>Jenis Kelamin</label>
                                <div class="rw-jk">
                                    <label id="jk-L"><input type="radio" name="jk" value="L"> Laki-Laki</label>
                                    <label id="jk-P"><input type="radio" name="jk" value="P"> Perempuan</label>
                                </div>
                                <div class="rw-err" id="e-jk"></div>
                            </div>
                            <div class="rw-field">
                                <label>Nomor HP / WhatsApp</label>
                                <input type="text" id="f-hp" class="rw-input" inputmode="numeric" placeholder="08xxxxxxxxxx">
                                <div class="rw-err" id="e-hp"></div>
                            </div>
                        </div>
                        @if (($forms ?? collect())->isEmpty())
                            {{-- Layanan tanpa form: tombol di DALAM card --}}
                            <div class="rw-actions" style="margin-top:26px;">
                                <a href="{{ route('antrian-online.layanan', $skpd->id) }}" class="rw-btn rw-btn-light" style="text-decoration:none;">Batal</a>
                                <button class="rw-btn rw-btn-primary" id="btn-step1">Lanjut ke Verifikasi
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="rw-side-card">
                        <span class="rw-side-ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg></span>
                        <div><h4>Butuh Bantuan</h4><p>Pastikan data yang Anda masukkan sesuai dengan E-KTP.</p></div>
                    </div>
                    <div class="rw-note"><b>Penting:</b> Data yang Anda kirimkan akan dienkripsi dan diproses sesuai kebijakan privasi negara untuk keperluan administrasi kependudukan.</div>
                </div>
            </div>

            @if (($forms ?? collect())->isNotEmpty())
            {{-- FORM PERSYARATAN — card terpisah, dipaginasi per-bagian --}}
            <div class="rw-card" id="rwFormCard" style="margin-top:22px;display:none;">
                <div class="rw-card-head">
                    <h3>Formulir Persyaratan</h3>
                    <p id="rwFormSub">Lengkapi data sesuai dokumen resmi. Petugas memverifikasi berkas fisik saat kedatangan.</p>
                </div>
                <div class="rw-card-body">
                    <div class="rw-formprog" id="rwFormProg"></div>
                    <div id="rwFormBody"></div>
                    <div class="rw-form-pager">
                        <button type="button" class="rw-btn rw-btn-light" id="rwPrev">‹ Sebelumnya</button>
                        <span class="rw-page-info" id="rwPageInfo"></span>
                        <button type="button" class="rw-btn rw-btn-primary" id="rwNext">Berikutnya ›</button>
                    </div>
                </div>
            </div>

            {{-- Aksi Step 1 --}}
            <div class="rw-actions" id="rwStep1Actions" style="margin-top:22px;">
                <a href="{{ route('antrian-online.layanan', $skpd->id) }}" class="rw-btn rw-btn-light" style="text-decoration:none;">Batal</a>
                <button class="rw-btn rw-btn-primary" id="btn-step1">Lanjut ke Verifikasi
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </div>
            @endif
        </div>

        {{-- STEP 2 --}}
        <div class="rw-step-panel" id="panel-2">
            <div class="rw-grid">
                <div class="rw-card">
                    <div class="rw-card-head"><h3>Verifikasi Wajah</h3><p>Pastikan wajah berada di dalam bingkai & pencahayaan cukup.</p></div>
                    <div class="rw-card-body">
                        <div class="rw-cam" id="camBox">
                            <video id="cam" autoplay playsinline muted></video>
                            <img id="shot" style="display:none;">
                            <div class="rw-cam-frame"></div>
                        </div>
                        <div class="rw-cam-hint" id="camHint">Memuat kamera & model deteksi wajah…</div>
                        <div class="rw-cam-btns">
                            <button class="rw-btn rw-btn-primary" id="btn-capture" disabled>Ambil Foto</button>
                            <button class="rw-btn rw-btn-light" id="btn-retake" style="display:none;">Ulang</button>
                        </div>
                        <div class="rw-actions">
                            <button class="rw-btn rw-btn-light" id="back-2">Kembali</button>
                            <button class="rw-btn rw-btn-primary" id="btn-step2" disabled>Lanjut ke Validasi
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="rw-side-card">
                        <span class="rw-side-ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v6m0 6v10M4.2 4.2l4.2 4.2m7.2 7.2 4.2 4.2"/></svg></span>
                        <div><h4>Petunjuk</h4><p>Pencahayaan cukup, lepas masker/kacamata gelap, posisi wajah menghadap kamera & tetap di dalam bingkai.</p></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 3 --}}
        <div class="rw-step-panel" id="panel-3">
            <div class="rw-card">
                <div class="rw-card-head"><h3>Tinjau Data</h3><p>Pastikan semua data yang Anda masukkan sudah benar sebelum mengambil antrean.</p></div>
                <div class="rw-card-body">
                    <div class="rw-review-grid">
                        <div>
                            <div class="fw-bold" style="color:var(--ao-primary-dark);margin-bottom:10px;font-weight:700;">Informasi Personal</div>
                            <div class="rw-row2">
                                <div><div class="rw-rv-label">NIK</div><div class="rw-rv-val" id="rv-nik">-</div></div>
                                <div><div class="rw-rv-label">Nama Lengkap</div><div class="rw-rv-val" id="rv-nama">-</div></div>
                            </div>
                            <div class="rw-row2">
                                <div><div class="rw-rv-label">Jenis Kelamin</div><div class="rw-rv-val" id="rv-jk">-</div></div>
                                <div><div class="rw-rv-label">Nomor HP/WA</div><div class="rw-rv-val" id="rv-hp">-</div></div>
                            </div>
                            <div class="fw-bold" style="color:var(--ao-primary-dark);margin:6px 0 4px;font-weight:700;">Detail Permohonan</div>
                            <div class="rw-permohonan">
                                <span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><path d="M6 12h1v6H6a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2zM18 12h-1v6h1a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2z"/></svg></span>
                                <div><div class="fw-bold" style="font-weight:700;">{{ $skpd->nama_skpd }}</div><div class="rw-rv-label">{{ $loket->nama_loket }}</div></div>
                            </div>
                            <div class="rw-rv-label" style="margin-top:12px;">Tanggal Pelayanan Dipilih</div>
                            <div class="rw-rv-val" id="rv-tanggal" style="color:var(--ao-primary);">-</div>
                        </div>
                        <div>
                            <div class="rw-rv-label" style="margin-bottom:8px;">Preview Verifikasi Wajah</div>
                            <img id="rv-photo" class="rw-rv-photo">
                        </div>
                    </div>
                    <div id="rv-form-summary" class="rw-fsummary"></div>

                    <label class="rw-agree">
                        <input type="checkbox" id="agree">
                        <span>Saya menyatakan dengan sadar bahwa seluruh data yang saya masukkan adalah <b>benar dan akurat</b>. Saya memahami bahwa pemalsuan data kependudukan dapat dikenakan sanksi sesuai UU No. 24 Tahun 2013 tentang Administrasi Kependudukan.</span>
                    </label>
                    <div class="rw-actions">
                        <button class="rw-btn rw-btn-light" id="back-3">Kembali</button>
                        <button class="rw-btn rw-btn-primary" id="btn-step3" disabled>Berikutnya
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 4 --}}
        <div class="rw-step-panel" id="panel-4">
            <div class="rw-success">
                <div class="rw-success-ic"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg></div>
                <h2 style="margin:6px 0;font-size:22px;color:var(--ao-primary-dark);">Pendaftaran Berhasil</h2>
                <p style="color:var(--ao-muted);margin:0;">Terima kasih! Pendaftaran Anda telah kami terima.</p>

                <div class="rw-ticket" id="rw-ticket">
                    <div class="rw-ticket-head">
                        <div><div style="font-weight:700;">Bukti Pendaftaran</div><div style="font-size:11px;opacity:.8;" id="tk-skpd"></div></div>
                        <span style="background:rgba(255,255,255,.2);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">SELESAI</span>
                    </div>
                    <div class="rw-ticket-body">
                        <div style="display:flex;justify-content:space-between;">
                            <div><div class="rw-rv-label">Nomor Antrian</div><div class="rw-no" id="tk-no"></div></div>
                            <div style="text-align:right;"><div class="rw-rv-label">Tanggal</div><div style="font-weight:700;" id="tk-tgl"></div></div>
                        </div>
                        <div id="tk-rollover" style="color:#dc2626;font-size:11.5px;margin-top:4px;display:none;"></div>
                        <table class="rw-ticket-table">
                            <tr><td style="width:80px;color:#64748b;">Layanan</td><td id="tk-layanan"></td></tr>
                            <tr><td style="color:#64748b;">Pemohon</td><td id="tk-pemohon"></td></tr>
                            <tr><td style="color:#64748b;">Lokasi</td><td id="tk-lokasi"></td></tr>
                        </table>
                        <div class="rw-qr" id="tk-qr"></div>
                        <div style="text-align:center;font-size:11px;color:#64748b;">Tunjukkan QR Code ke petugas atau pindai di mesin mandiri di lokasi layanan.</div>
                    </div>
                </div>

                <div id="tk-expiry" style="margin-top:16px;font-size:12.5px;font-weight:700;color:#d97706;"></div>
                <div class="rw-note" style="max-width:460px;margin:12px auto 0;text-align:left;">
                    <b>Simpan bukti ini.</b> Tiket tersimpan otomatis di <b>perangkat ini saja</b> selama <b>10 menit</b>
                    (aman — tidak terlihat oleh orang lain) sehingga tetap muncul bila halaman ter-refresh atau tertutup.
                    Setelah 10 menit tampilan ini hilang, jadi <b>unduh / cetak</b> tiket sebagai bukti.
                    Bila tiket telanjur hilang, Konfirmasi <b>Nomor Antrean</b> Anda ke petugas MPP.
                </div>
                <div style="display:flex;gap:10px;justify-content:center;margin-top:18px;flex-wrap:wrap;">
                    <button class="rw-btn rw-btn-primary" id="btn-download">Unduh Tiket (Gambar)</button>
                    <button class="rw-btn rw-btn-light" onclick="window.print()">Cetak</button>
                    <a href="{{ route('antrian-online') }}" class="rw-btn rw-btn-light" style="text-decoration:none;">Beranda</a>
                </div>
                <div style="margin-top:10px;">
                    <a href="#" id="btn-daftar-baru" style="font-size:12.5px;color:var(--ao-muted);">Kembali &amp; daftar antrean baru</a>
                </div>
            </div>
        </div>

        @endif
    </div>
</div>

{{-- Modal konfirmasi --}}
<div class="rw-modal" id="rwModal">
    <div class="rw-modal-box">
        <div class="rw-success-ic" style="background:#eef2ff;color:var(--ao-primary);"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
        <h3 style="margin:6px 0;font-size:18px;">Ambil Antrian?</h3>
        <p style="color:var(--ao-muted);font-size:13px;margin:0 0 18px;">Pastikan data sudah benar. Nomor antrean akan diterbitkan setelah konfirmasi.</p>
        <div style="display:flex;gap:10px;">
            <button class="rw-btn rw-btn-light" id="m-batal" style="flex:1;justify-content:center;">Batal</button>
            <button class="rw-btn rw-btn-primary" id="m-ambil" style="flex:1;justify-content:center;">Ambil Antrian</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@unless ($kuotaHabis)
<script src="{{ asset('vendor/faceapi/face-api.min.js') }}"></script>
<script src="{{ asset('vendor/qrcode/qrcode.min.js') }}"></script>
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const ROUTES = {
        draft:  @json(route('antrian-online.registrasi.draft', $loket->id)),
        submit: @json(route('antrian-online.registrasi.submit', $loket->id)),
    };
    const MODEL_URI = @json(asset('vendor/faceapi/model'));
    const draft = @json($draft ?? []);
    const LOKET_ID = @json($loket->id);
    const TICKET_TTL_MS = 10 * 60 * 1000;          // 10 menit
    const LS_KEY = 'ao_ticket_' + LOKET_ID;        // disimpan hanya di perangkat ini

    const state = { step: 1, foto: null, tanggal: null, tanggalLabel: null };
    state.form = {};
    let stream = null, faceReady = false;

    // ===== Form persyaratan (dinamis dari skema) =====
    const FORMS = @json($forms ?? []);
    const FORM_LS_KEY = 'ao_form_' + LOKET_ID;
    const FORM_OPTIONAL = true;   // seluruh field form persyaratan bersifat opsional
    const WILAYAH = {
        prov: @json(url('wilayah/provinsi')),
        kab:  @json(url('wilayah/kabupaten')),
        kec:  @json(url('wilayah/kecamatan')),
        desa: @json(url('wilayah/desa')),
    };
    FORMS.forEach(function (f) { state.form[f.id] = state.form[f.id] || {}; });

    // Ambang validasi wajah (bisa disetel). Occlusion/tertutup menurunkan skor & ukuran.
    const FACE_MIN_SCORE = 0.78;   // keyakinan minimal deteksi wajah
    const FACE_MIN_AREA  = 0.08;   // proporsi minimal area wajah terhadap frame

    // ---- Prefill dari draft (resume 10 menit) ----
    if (draft) {
        if (draft.nik)  document.getElementById('f-nik').value = draft.nik;
        if (draft.nama) document.getElementById('f-nama').value = draft.nama;
        if (draft.no_hp) document.getElementById('f-hp').value = draft.no_hp;
        if (draft.jk) { const r = document.querySelector('input[name=jk][value="'+draft.jk+'"]'); if (r) { r.checked = true; markJk(); } }
        if (draft.foto) { state.foto = 'data:image/jpeg;base64,' + draft.foto; }
    }

    function markJk() {
        document.getElementById('jk-L').classList.toggle('sel', document.querySelector('input[name=jk][value=L]').checked);
        document.getElementById('jk-P').classList.toggle('sel', document.querySelector('input[name=jk][value=P]').checked);
    }
    document.querySelectorAll('input[name=jk]').forEach(r => r.addEventListener('change', markJk));

    // Pilih hari pelayanan
    document.querySelectorAll('#rwDays .rw-day:not([disabled])').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#rwDays .rw-day').forEach(b => b.classList.remove('sel'));
            this.classList.add('sel');
            state.tanggal = this.dataset.tanggal;
            state.tanggalLabel = this.querySelector('.rw-day-name').textContent + ', ' + this.querySelector('.rw-day-date').textContent;
            showErr('e-tanggal', '');
        });
    });

    function go(step) {
        state.step = step;
        document.querySelectorAll('.rw-step-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('panel-' + step).classList.add('active');
        document.querySelectorAll('#rwSteps .rw-step').forEach(s => {
            const n = +s.dataset.step;
            s.classList.toggle('active', n === step);
            s.classList.toggle('done', n < step);
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
        if (step === 2) startCamera();
        else stopCamera();
        if (step === 3) fillReview();
    }

    // ===== Tiket: render, simpan per-perangkat (10 mnt), countdown, unduh =====
    function renderTicket(data) {
        window.__ticket = data;
        document.getElementById('tk-skpd').textContent = data.skpd || '';
        document.getElementById('tk-no').textContent = data.no_antrian || '';
        document.getElementById('tk-tgl').textContent = data.tanggal || '';
        document.getElementById('tk-layanan').textContent = data.layanan || '';
        document.getElementById('tk-pemohon').textContent = data.pemohon || '';
        document.getElementById('tk-lokasi').textContent = data.lokasi || '';
        const ro = document.getElementById('tk-rollover');
        if (!data.is_hari_ini) { ro.style.display = 'block'; ro.textContent = '*Datang ke lokasi pada tanggal ' + data.tanggal; }
        else { ro.style.display = 'none'; }
        const qrBox = document.getElementById('tk-qr'); qrBox.innerHTML = '';
        try {
            new QRCode(qrBox, { text: String(data.qr), width: 150, height: 150,
                correctLevel: (window.QRCode && QRCode.CorrectLevel) ? QRCode.CorrectLevel.M : undefined });
        } catch (e) {}
    }

    function persistTicket(data) {
        try { localStorage.setItem(LS_KEY, JSON.stringify({ data: data, exp: Date.now() + TICKET_TTL_MS })); } catch (e) {}
    }
    function clearTicket() { try { localStorage.removeItem(LS_KEY); } catch (e) {} }

    let expiryTimer = null;
    function startExpiryCountdown(exp) {
        const el = document.getElementById('tk-expiry');
        function tick() {
            const ms = exp - Date.now();
            if (ms <= 0) {
                el.textContent = 'Masa simpan tiket di perangkat ini telah berakhir. Pastikan Anda sudah mengunduh/mencetak bukti.';
                el.style.color = '#dc2626';
                clearTicket();
                if (expiryTimer) { clearInterval(expiryTimer); expiryTimer = null; }
                return;
            }
            const m = Math.floor(ms / 60000), s = Math.floor((ms % 60000) / 1000);
            el.style.color = '#d97706';
            el.textContent = '⏱ Tersimpan di perangkat ini ' + m + ':' + (s < 10 ? '0' : '') + s + ' lagi.';
        }
        tick();
        if (expiryTimer) clearInterval(expiryTimer);
        expiryTimer = setInterval(tick, 1000);
    }

    function trunc(ctx, text, maxW) {
        text = String(text);
        if (ctx.measureText(text).width <= maxW) return text;
        while (text.length > 1 && ctx.measureText(text + '…').width > maxW) text = text.slice(0, -1);
        return text + '…';
    }

    function triggerDownload(href, filename, silent) {
        const a = document.createElement('a');
        a.href = href; a.download = filename; a.rel = 'noopener';
        document.body.appendChild(a); a.click();
        setTimeout(function () { a.remove(); }, 0);
        if (!silent && window.aoToast) window.aoToast('Tiket diunduh sebagai gambar. Cek folder Unduhan / ikon unduhan di browser.');
    }

    function downloadTicketPng(silent) {
        const d = window.__ticket;
        if (!d) { if (window.aoToast) window.aoToast('Tiket belum siap.'); return; }
        const W = 460, H = 700, s = 2;
        const cv = document.createElement('canvas');
        cv.width = W * s; cv.height = H * s;
        const ctx = cv.getContext('2d');
        ctx.scale(s, s);
        ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, W, H);
        ctx.strokeStyle = '#e2e8f0'; ctx.lineWidth = 1; ctx.strokeRect(10, 10, W - 20, H - 20);
        ctx.fillStyle = '#111827'; ctx.fillRect(10, 10, W - 20, 64);
        ctx.fillStyle = '#ffffff';
        ctx.font = '700 18px Arial, sans-serif'; ctx.fillText('Bukti Pendaftaran Antrean', 28, 40);
        ctx.font = '12px Arial, sans-serif'; ctx.fillText(trunc(ctx, d.skpd || '', W - 56), 28, 60);
        let y = 112;
        ctx.fillStyle = '#64748b'; ctx.font = '12px Arial, sans-serif'; ctx.fillText('Nomor Antrian', 28, y);
        ctx.fillStyle = '#4f46e5'; ctx.font = '700 40px Arial, sans-serif'; ctx.fillText(d.no_antrian || '', 28, y + 38);
        ctx.textAlign = 'right';
        ctx.fillStyle = '#64748b'; ctx.font = '12px Arial, sans-serif'; ctx.fillText('Tanggal', W - 28, y);
        ctx.fillStyle = '#0f172a'; ctx.font = '700 15px Arial, sans-serif'; ctx.fillText(d.tanggal || '', W - 28, y + 22);
        ctx.textAlign = 'left';
        y += 76;
        [['Layanan', d.layanan], ['Pemohon', d.pemohon], ['Lokasi', d.lokasi]].forEach(function (r) {
            ctx.fillStyle = '#64748b'; ctx.font = '12px Arial, sans-serif'; ctx.fillText(r[0], 28, y);
            ctx.fillStyle = '#0f172a'; ctx.font = '600 14px Arial, sans-serif'; ctx.fillText(trunc(ctx, r[1] || '-', W - 150), 120, y);
            y += 26;
        });
        const qr = document.querySelector('#tk-qr canvas') || document.querySelector('#tk-qr img');
        const qrSize = 170, qx = (W - qrSize) / 2, qy = y + 14;
        function finish() {
            ctx.textAlign = 'center';
            ctx.fillStyle = '#64748b'; ctx.font = '11px Arial, sans-serif';
            ctx.fillText('Tunjukkan / pindai QR ini di lokasi layanan.', W / 2, qy + qrSize + 26);
            ctx.font = '700 11px Arial, sans-serif';
            ctx.fillText('MPP Kabupaten Deli Serdang', W / 2, qy + qrSize + 44);
            ctx.textAlign = 'left';
            const fname = 'Tiket-' + (d.no_antrian || 'antrean') + '.png';
            function dataUrlFallback() {
                try { triggerDownload(cv.toDataURL('image/png'), fname, silent); }
                catch (e2) { if (window.aoToast) window.aoToast('Gagal mengunduh otomatis. Klik tombol "Unduh Tiket (Gambar)" atau "Cetak".'); }
            }
            try {
                if (cv.toBlob) {
                    cv.toBlob(function (blob) {
                        if (!blob) { dataUrlFallback(); return; }
                        const url = URL.createObjectURL(blob);
                        triggerDownload(url, fname, silent);
                        setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
                    }, 'image/png');
                } else { dataUrlFallback(); }
            } catch (e) { dataUrlFallback(); }
        }
        if (qr && qr.tagName === 'IMG') {
            if (qr.complete && qr.naturalWidth) { ctx.drawImage(qr, qx, qy, qrSize, qrSize); finish(); }
            else { qr.onload = function () { ctx.drawImage(qr, qx, qy, qrSize, qrSize); finish(); }; }
        } else if (qr) {
            ctx.drawImage(qr, qx, qy, qrSize, qrSize); finish();
        } else { finish(); }
    }

    async function saveDraft(payload) {
        const fd = new FormData();
        Object.keys(payload).forEach(k => fd.append(k, payload[k]));
        const r = await fetch(ROUTES.draft, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: fd });
        return { ok: r.ok, data: await r.json().catch(() => ({})) };
    }

    function showErr(id, msg) { const e = document.getElementById(id); e.textContent = msg; e.style.display = msg ? 'block' : 'none'; }

    // ===== Renderer form persyaratan (dinamis) =====
    function fesc(s) { return String(s == null ? '' : s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c])); }
    function fval(fid, k) { return state.form[fid] ? state.form[fid][k] : undefined; }
    function setVal(fid, k, v) { if (!state.form[fid]) state.form[fid] = {}; state.form[fid][k] = v; persistForm(); }
    function persistForm() { try { localStorage.setItem(FORM_LS_KEY, JSON.stringify(state.form)); } catch (e) {} }
    function clearForm() { try { localStorage.removeItem(FORM_LS_KEY); } catch (e) {} }

    (function restoreForm() {
        try {
            var s = JSON.parse(localStorage.getItem(FORM_LS_KEY) || 'null');
            if (s && typeof s === 'object') Object.keys(s).forEach(k => state.form[k] = Object.assign(state.form[k] || {}, s[k]));
        } catch (e) {}
        FORMS.forEach(f => ((f.skema && f.skema.sections) || []).forEach(sec => (sec.fields || []).forEach(fl => {
            if (fl.default != null && (state.form[f.id][fl.key] == null || state.form[f.id][fl.key] === '')) state.form[f.id][fl.key] = fl.default;
        })));
    })();

    function buildLabel(field) {
        const lbl = document.createElement('label');
        lbl.innerHTML = fesc(field.label) + ((field.required && !FORM_OPTIONAL) ? ' <span style="color:#dc2626">*</span>' : '');
        return lbl;
    }

    function renderField(fid, field) {
        const wrap = document.createElement('div');
        wrap.className = 'rw-ffield'; wrap.dataset.key = field.key; wrap.dataset.fid = fid;
        if (field.showIf) wrap.dataset.showif = JSON.stringify(field.showIf);
        wrap.appendChild(buildLabel(field));
        if (field.type === 'repeater') { wrap.appendChild(renderRepeater(fid, field)); return wrap; }
        if (field.type === 'wilayah') { wrap.appendChild(renderWilayah(fid, field)); return wrap; }
        const cur = fval(fid, field.key);
        let input;
        if (field.type === 'textarea') {
            input = document.createElement('textarea'); input.className = 'rw-input'; input.rows = 2; input.value = cur || '';
            input.addEventListener('input', () => setVal(fid, field.key, input.value));
        } else if (field.type === 'select') {
            input = document.createElement('select'); input.className = 'rw-input';
            input.appendChild(new Option('-- Pilih --', ''));
            (field.options || []).forEach(o => { const v = typeof o === 'object' ? o.value : o, l = typeof o === 'object' ? o.label : o; const op = new Option(l, v); if (String(cur) === String(v)) op.selected = true; input.appendChild(op); });
            input.addEventListener('change', () => { setVal(fid, field.key, input.value); refreshVisibility(fid); });
        } else if (field.type === 'radio' || field.type === 'checkbox') {
            const multi = field.type === 'checkbox';
            input = document.createElement('div'); input.className = 'rw-optgrid';
            (field.options || []).forEach(o => {
                const v = typeof o === 'object' ? o.value : o, l = typeof o === 'object' ? o.label : o;
                const item = document.createElement('label'); item.className = 'rw-optitem';
                const inp = document.createElement('input'); inp.type = multi ? 'checkbox' : 'radio'; inp.name = fid + '_' + field.key; inp.value = v;
                const arr = Array.isArray(cur) ? cur : [];
                if (multi ? arr.indexOf(v) >= 0 : String(cur) === String(v)) inp.checked = true;
                inp.addEventListener('change', () => {
                    if (multi) { const set = new Set(Array.isArray(fval(fid, field.key)) ? fval(fid, field.key) : []); inp.checked ? set.add(v) : set.delete(v); setVal(fid, field.key, Array.from(set)); }
                    else { setVal(fid, field.key, v); refreshVisibility(fid); }
                });
                const sp = document.createElement('span'); sp.textContent = l;
                item.appendChild(inp); item.appendChild(sp); input.appendChild(item);
            });
        } else if (field.type === 'file') {
            input = document.createElement('div');
            const fi = document.createElement('input'); fi.type = 'file'; fi.className = 'rw-input'; if (field.accept) fi.accept = field.accept;
            const info = document.createElement('div'); info.style.cssText = 'font-size:12px;color:var(--ao-muted);margin-top:4px;';
            if (cur && cur.name) info.textContent = 'Terpilih: ' + cur.name;
            fi.addEventListener('change', () => {
                const file = fi.files[0]; if (!file) return;
                const maxKb = field.maxKb || 3072;
                if (file.size > maxKb * 1024) { info.textContent = 'File melebihi ' + Math.round(maxKb / 1024) + ' MB'; info.style.color = '#dc2626'; fi.value = ''; return; }
                const rd = new FileReader();
                rd.onload = () => { setVal(fid, field.key, { name: file.name, dataUrl: rd.result }); info.textContent = 'Terpilih: ' + file.name; info.style.color = '#16a34a'; };
                rd.readAsDataURL(file);
            });
            input.appendChild(fi); input.appendChild(info);
        } else {
            input = document.createElement('input'); input.className = 'rw-input';
            input.type = field.type === 'number' ? 'number' : (field.type === 'date' ? 'date' : (field.type === 'time' ? 'time' : 'text'));
            if (field.inputmode) input.inputMode = field.inputmode;
            if (field.maxlength) input.maxLength = field.maxlength;
            input.value = cur != null ? cur : '';
            input.addEventListener('input', () => setVal(fid, field.key, input.value));
        }
        wrap.appendChild(input);
        const err = document.createElement('div'); err.className = 'rw-err rw-ferr'; wrap.appendChild(err);
        return wrap;
    }

    function renderRepeater(fid, field) {
        const box = document.createElement('div');
        const rows = document.createElement('div'); box.appendChild(rows);
        let data = fval(fid, field.key); if (!Array.isArray(data)) data = [];
        const save = () => setVal(fid, field.key, data);
        function redraw() {
            rows.innerHTML = '';
            data.forEach(function (rowObj, idx) {
                const row = document.createElement('div'); row.className = 'rw-repeater-row';
                (field.columns || []).forEach(function (col) {
                    const cell = document.createElement('div'); cell.className = 'rw-repeater-cell';
                    const cl = document.createElement('div'); cl.className = 'rw-repeater-lbl'; cl.textContent = col.label; cell.appendChild(cl);
                    let inp;
                    if (col.type === 'select') {
                        inp = document.createElement('select'); inp.className = 'rw-input'; inp.appendChild(new Option('-', ''));
                        (col.options || []).forEach(o => { const v = typeof o === 'object' ? o.value : o, l = typeof o === 'object' ? o.label : o; const op = new Option(l, v); if (String(rowObj[col.key]) === String(v)) op.selected = true; inp.appendChild(op); });
                    } else { inp = document.createElement('input'); inp.className = 'rw-input'; inp.type = col.type === 'number' ? 'number' : (col.type === 'date' ? 'date' : 'text'); inp.value = rowObj[col.key] || ''; }
                    const upd = () => { rowObj[col.key] = inp.value; save(); };
                    inp.addEventListener('input', upd); inp.addEventListener('change', upd);
                    cell.appendChild(inp); row.appendChild(cell);
                });
                const del = document.createElement('button'); del.type = 'button'; del.className = 'rw-repeater-del'; del.innerHTML = '&times;';
                del.addEventListener('click', () => { data.splice(idx, 1); save(); redraw(); });
                row.appendChild(del); rows.appendChild(row);
            });
        }
        redraw();
        const add = document.createElement('button'); add.type = 'button'; add.className = 'rw-btn rw-btn-light'; add.style.marginTop = '4px'; add.textContent = '+ Tambah Baris';
        add.addEventListener('click', () => { data.push({}); save(); redraw(); });
        box.appendChild(add);
        return box;
    }

    function renderWilayah(fid, field) {
        const box = document.createElement('div'); box.className = 'rw-wilayah-grid';
        const cur = fval(fid, field.key) || {};

        function mk(label, ph) {
            const w = document.createElement('div');
            const l = document.createElement('div'); l.className = 'rw-wilayah-lbl'; l.textContent = label;
            const s = document.createElement('select'); s.className = 'rw-input';
            s.appendChild(new Option('-- ' + ph + ' --', ''));
            w.appendChild(l); w.appendChild(s); box.appendChild(w);
            return s;
        }
        const selProv = mk('Provinsi', 'Pilih Provinsi');
        const selKab = mk('Kabupaten/Kota', 'Pilih Kabupaten');
        const selKec = mk('Kecamatan', 'Pilih Kecamatan');
        const dw = document.createElement('div');
        const dl = document.createElement('div'); dl.className = 'rw-wilayah-lbl'; dl.textContent = 'Desa/Kelurahan';
        const selDesa = document.createElement('select'); selDesa.className = 'rw-input'; selDesa.appendChild(new Option('-- Pilih Desa --', ''));
        const txtDesa = document.createElement('input'); txtDesa.className = 'rw-input'; txtDesa.placeholder = 'Ketik Desa/Kelurahan'; txtDesa.style.display = 'none'; txtDesa.style.marginTop = '6px';
        dw.appendChild(dl); dw.appendChild(selDesa); dw.appendChild(txtDesa); box.appendChild(dw);

        function txt(s) { return s.value && s.selectedOptions[0] ? s.selectedOptions[0].textContent : ''; }
        function save() {
            setVal(fid, field.key, {
                provinsi_id: selProv.value, provinsi: txt(selProv),
                kabupaten_id: selKab.value, kabupaten: txt(selKab),
                kecamatan_id: selKec.value, kecamatan: txt(selKec),
                desa_id: selDesa.value, desa: selDesa.value ? txt(selDesa) : (txtDesa.value || ''),
            });
        }
        function fill(sel, list, selId) {
            sel.length = 1;
            (list || []).forEach(o => { const op = new Option(o.nama, o.id); if (String(selId) === String(o.id)) op.selected = true; sel.appendChild(op); });
        }
        async function load(url) { try { const r = await fetch(url, { headers: { 'Accept': 'application/json' } }); return r.ok ? await r.json() : []; } catch (e) { return []; } }

        selProv.addEventListener('change', async () => {
            selKab.length = 1; selKec.length = 1; selDesa.length = 1; selDesa.style.display = ''; txtDesa.style.display = 'none'; save();
            if (selProv.value) fill(selKab, await load(WILAYAH.kab + '/' + selProv.value));
        });
        selKab.addEventListener('change', async () => {
            selKec.length = 1; selDesa.length = 1; selDesa.style.display = ''; txtDesa.style.display = 'none'; save();
            if (selKab.value) fill(selKec, await load(WILAYAH.kec + '/' + selKab.value));
        });
        selKec.addEventListener('change', async () => {
            selDesa.length = 1; save();
            if (selKec.value) {
                const d = await load(WILAYAH.desa + '/' + selKec.value);
                if (d.length) { fill(selDesa, d); selDesa.style.display = ''; txtDesa.style.display = 'none'; }
                else { selDesa.style.display = 'none'; txtDesa.style.display = ''; }
            }
        });
        selDesa.addEventListener('change', save);
        txtDesa.addEventListener('input', save);

        (async () => {
            fill(selProv, await load(WILAYAH.prov), cur.provinsi_id);
            if (cur.provinsi_id) fill(selKab, await load(WILAYAH.kab + '/' + cur.provinsi_id), cur.kabupaten_id);
            if (cur.kabupaten_id) fill(selKec, await load(WILAYAH.kec + '/' + cur.kabupaten_id), cur.kecamatan_id);
            if (cur.kecamatan_id) {
                const d = await load(WILAYAH.desa + '/' + cur.kecamatan_id);
                if (d.length) { fill(selDesa, d, cur.desa_id); }
                else { selDesa.style.display = 'none'; txtDesa.style.display = ''; txtDesa.value = cur.desa || ''; }
            }
        })();

        return box;
    }

    function refreshVisibility(fid) {
        document.querySelectorAll('#rwFormBody .rw-ffield[data-showif]').forEach(function (el) {
            if (el.dataset.fid !== String(fid)) return;
            let cond; try { cond = JSON.parse(el.dataset.showif); } catch (e) { return; }
            const v = fval(fid, cond.field);
            const arr = Array.isArray(v) ? v : [v];
            el.style.display = (cond.in || []).some(x => arr.indexOf(x) >= 0 || String(v) === String(x)) ? '' : 'none';
        });
    }

    // ===== Form persyaratan: card terpisah, paginasi per-section =====
    let formPages = [];
    FORMS.forEach(function (f) {
        ((f.skema && f.skema.sections) || []).forEach(function (sec) {
            formPages.push({ formId: f.id, kode: f.kode, nama: f.nama, section: sec });
        });
    });
    let formPage = 0;

    function scrollFormTop() { const c = document.getElementById('rwFormCard'); if (c) c.scrollIntoView({ block: 'start', behavior: 'smooth' }); }

    function renderFormCard() {
        const card = document.getElementById('rwFormCard');
        if (!card) return;
        if (!formPages.length) { card.style.display = 'none'; return; }
        card.style.display = '';
        formPage = Math.max(0, Math.min(formPage, formPages.length - 1));
        const page = formPages[formPage];
        document.getElementById('rwFormSub').textContent = page.nama + ' (' + page.kode + ') — ' + (page.section.title || '');
        document.getElementById('rwFormProg').textContent = 'Bagian ' + (formPage + 1) + ' dari ' + formPages.length;
        document.getElementById('rwPageInfo').textContent = (formPage + 1) + ' / ' + formPages.length;
        const body = document.getElementById('rwFormBody'); body.innerHTML = '';
        if (page.section.note) { const n = document.createElement('div'); n.className = 'rw-note'; n.style.marginBottom = '14px'; n.textContent = page.section.note; body.appendChild(n); }
        (page.section.fields || []).forEach(fl => body.appendChild(renderField(page.formId, fl)));
        refreshVisibility(page.formId);
        document.getElementById('rwPrev').style.visibility = formPage === 0 ? 'hidden' : 'visible';
        document.getElementById('rwNext').style.visibility = formPage === formPages.length - 1 ? 'hidden' : 'visible';
    }

    function fieldVisibleByState(formId, fl) {
        if (!fl.showIf) return true;
        const cv = fval(formId, fl.showIf.field);
        const arr = Array.isArray(cv) ? cv : [cv];
        return (fl.showIf.in || []).some(x => arr.indexOf(x) >= 0 || String(cv) === String(x));
    }

    function validatePageAt(index, showErrors) {
        const page = formPages[index]; if (!page) return true;
        let ok = true;
        (page.section.fields || []).forEach(function (fl) {
            if (!fieldVisibleByState(page.formId, fl)) return;
            const v = fval(page.formId, fl.key);
            const empty = (v == null || v === '' || (Array.isArray(v) && v.length === 0) || (fl.type === 'file' && !(v && (v.dataUrl || v.file))));
            let msg = null;
            if (fl.required && !FORM_OPTIONAL && empty) msg = 'Wajib diisi';
            else if (fl.rules && /digits:16/.test(fl.rules) && v && !/^\d{16}$/.test(v)) msg = 'Harus 16 digit angka';
            if (msg) ok = false;
            if (showErrors) {
                const wrap = document.querySelector('#rwFormBody .rw-ffield[data-fid="' + page.formId + '"][data-key="' + fl.key + '"]');
                const err = wrap && wrap.querySelector('.rw-ferr');
                if (err) { err.textContent = msg || ''; err.style.display = msg ? 'block' : 'none'; }
            }
        });
        return ok;
    }

    function firstInvalidPage() {
        for (let i = 0; i < formPages.length; i++) { if (!validatePageAt(i, false)) return i; }
        return -1;
    }

    var rwPrev = document.getElementById('rwPrev'), rwNext = document.getElementById('rwNext');
    if (rwPrev) rwPrev.addEventListener('click', function () { if (formPage > 0) { formPage--; renderFormCard(); scrollFormTop(); } });
    if (rwNext) rwNext.addEventListener('click', function () {
        if (!validatePageAt(formPage, true)) { window.aoToast('Lengkapi bagian ini dulu.'); return; }
        if (formPage < formPages.length - 1) { formPage++; renderFormCard(); scrollFormTop(); }
    });

    renderFormCard();

    function buildFormSummary() {
        const host = document.getElementById('rv-form-summary'); if (!host) return;
        host.innerHTML = '';
        FORMS.forEach(function (f) {
            const vals = state.form[f.id] || {};
            const card = document.createElement('div'); card.className = 'rw-fsum-form';
            const h = document.createElement('h5'); h.textContent = f.nama + ' (' + f.kode + ')'; card.appendChild(h);
            ((f.skema && f.skema.sections) || []).forEach(function (sec) {
                const shown = [];
                (sec.fields || []).forEach(function (fl) {
                    const v = vals[fl.key];
                    if (v == null || v === '' || (Array.isArray(v) && v.length === 0)) return;
                    let disp;
                    if (fl.type === 'repeater') disp = (Array.isArray(v) ? v.length : 0) + ' baris';
                    else if (fl.type === 'file') disp = 'Terlampir: ' + (v.name || 'dokumen');
                    else if (fl.type === 'wilayah') { disp = [v.desa, v.kecamatan, v.kabupaten, v.provinsi].filter(Boolean).join(', '); if (!disp) return; }
                    else if (Array.isArray(v)) disp = v.join(', ');
                    else disp = String(v);
                    shown.push({ label: fl.label, disp });
                });
                if (shown.length) {
                    const st = document.createElement('div'); st.className = 'rw-fsum-sec'; st.textContent = sec.title || ''; card.appendChild(st);
                    shown.forEach(r => { const row = document.createElement('div'); row.className = 'rw-fsum-row'; row.innerHTML = '<span class="k">' + fesc(r.label) + '</span><span class="v">' + fesc(r.disp) + '</span>'; card.appendChild(row); });
                }
            });
            host.appendChild(card);
        });
    }

    // ---- STEP 1 ----
    document.getElementById('btn-step1').addEventListener('click', async function () {
        ['e-nik','e-nama','e-jk','e-hp','e-tanggal'].forEach(i => showErr(i, ''));
        const nik = document.getElementById('f-nik').value.trim();
        const nama = document.getElementById('f-nama').value.trim();
        const jkEl = document.querySelector('input[name=jk]:checked');
        const hp = document.getElementById('f-hp').value.trim();
        let ok = true;
        if (!state.tanggal) { showErr('e-tanggal', 'Pilih hari pelayanan terlebih dahulu'); ok = false; }
        if (!/^\d{16}$/.test(nik)) { showErr('e-nik', 'NIK harus 16 digit angka'); ok = false; }
        if (!nama) { showErr('e-nama', 'Nama wajib diisi'); ok = false; }
        if (!jkEl) { showErr('e-jk', 'Pilih jenis kelamin'); ok = false; }
        if (!/^\d{10,14}$/.test(hp)) { showErr('e-hp', 'Nomor HP/WA 10-14 digit'); ok = false; }
        if (!ok) return;

        // Form persyaratan (card terpisah, paginasi) juga wajib lengkap
        if (formPages.length) {
            const bad = firstInvalidPage();
            if (bad >= 0) {
                formPage = bad; renderFormCard(); validatePageAt(bad, true); scrollFormTop();
                window.aoToast('Lengkapi form persyaratan yang wajib diisi.');
                return;
            }
        }

        this.disabled = true;
        const res = await saveDraft({ step: 1, nik, nama, jk: jkEl.value, no_hp: hp, form_values: JSON.stringify(state.form) });
        this.disabled = false;
        if (!res.ok) {
            if (res.data.errors) { for (const k in res.data.errors) showErr('e-' + (k === 'no_hp' ? 'hp' : k), res.data.errors[k][0]); }
            else window.aoToast(res.data.message || 'Gagal menyimpan data');
            return;
        }
        go(2);
    });

    // ---- STEP 2: kamera + face-api ----
    async function startCamera() {
        const hint = document.getElementById('camHint');
        if (!faceReady) {
            try { await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URI); faceReady = true; }
            catch (e) { hint.textContent = 'Gagal memuat model deteksi wajah.'; }
        }
        if (state.foto) { // resume: tampilkan foto tersimpan
            showShot(state.foto);
            document.getElementById('btn-step2').disabled = false;
            hint.textContent = 'Foto tersimpan. Klik "Ulang" untuk ganti.';
            return;
        }
        // Kamera butuh secure context: HTTPS atau localhost/127.0.0.1. Akses via IP/HTTP tidak diizinkan.
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            hint.textContent = window.isSecureContext
                ? 'Browser ini tidak mendukung akses kamera.'
                : 'Kamera butuh HTTPS atau localhost. Akses lewat alamat IP/HTTP tidak diizinkan browser — buka via HTTPS.';
            return;
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 }, audio: false });
            const v = document.getElementById('cam');
            v.srcObject = stream; v.style.display = ''; document.getElementById('shot').style.display = 'none';
            document.getElementById('btn-capture').disabled = false;
            document.getElementById('btn-retake').style.display = 'none';
            hint.textContent = 'Posisikan wajah di dalam bingkai lalu klik "Ambil Foto".';
        } catch (e) {
            let msg = 'Tidak dapat mengakses kamera.';
            if (e && (e.name === 'NotAllowedError' || e.name === 'SecurityError')) {
                msg = 'Akses kamera ditolak. Klik ikon kunci/info di address bar → Kamera → Izinkan. (Brave: turunkan Shields untuk situs ini.)';
            } else if (e && (e.name === 'NotFoundError' || e.name === 'OverconstrainedError')) {
                msg = 'Kamera tidak ditemukan di perangkat ini.';
            } else if (e && e.name === 'NotReadableError') {
                msg = 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi itu lalu coba lagi.';
            }
            hint.textContent = msg;
            document.getElementById('btn-capture').disabled = true;
        }
    }
    function stopCamera() { if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; } }
    function showShot(src) {
        const img = document.getElementById('shot'); img.src = src; img.style.display = '';
        document.getElementById('cam').style.display = 'none';
        document.getElementById('btn-capture').style.display = 'none';
        document.getElementById('btn-retake').style.display = '';
    }

    document.getElementById('btn-capture').addEventListener('click', async function () {
        const hint = document.getElementById('camHint');
        const v = document.getElementById('cam');
        const cv = document.createElement('canvas');
        cv.width = v.videoWidth || 640; cv.height = v.videoHeight || 480;
        cv.getContext('2d').drawImage(v, 0, 0, cv.width, cv.height);
        hint.textContent = 'Memeriksa wajah…';
        if (faceReady) {
            let det = null;
            try { det = await faceapi.detectSingleFace(cv, new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.4 })); } catch (e) {}
            if (!det) { hint.textContent = '⚠️ Wajah tidak terdeteksi. Pastikan seluruh wajah terlihat & coba lagi.'; return; }

            const box = det.box;
            const score = det.score || 0;
            const areaFrac = (box.width * box.height) / (cv.width * cv.height);
            const cx = (box.x + box.width / 2) / cv.width;
            const cy = (box.y + box.height / 2) / cv.height;
            console.log('[face]', { score: +score.toFixed(3), areaFrac: +areaFrac.toFixed(3), cx: +cx.toFixed(2), cy: +cy.toFixed(2) });

            if (score < FACE_MIN_SCORE) { hint.textContent = '⚠️ Wajah kurang jelas / sebagian tertutup. Pastikan seluruh wajah terlihat tanpa terhalang.'; return; }
            if (areaFrac < FACE_MIN_AREA) { hint.textContent = '⚠️ Wajah terlalu jauh. Dekatkan wajah ke kamera.'; return; }
            if (cx < 0.18 || cx > 0.82 || cy < 0.12 || cy > 0.88) { hint.textContent = '⚠️ Posisikan wajah di tengah bingkai.'; return; }
        }
        const dataUrl = cv.toDataURL('image/jpeg', 0.9);
        state.foto = dataUrl;
        showShot(dataUrl);
        stopCamera();
        document.getElementById('btn-step2').disabled = false;
        hint.textContent = '✓ Wajah terdeteksi. Lanjutkan atau ambil ulang.';
    });

    document.getElementById('btn-retake').addEventListener('click', function () {
        state.foto = null;
        document.getElementById('btn-capture').style.display = '';
        document.getElementById('btn-step2').disabled = true;
        startCamera();
    });

    document.getElementById('back-2').addEventListener('click', () => go(1));

    document.getElementById('btn-step2').addEventListener('click', async function () {
        if (!state.foto) { window.aoToast('Ambil foto wajah dulu'); return; }
        this.disabled = true;
        const res = await saveDraft({ step: 2, foto: state.foto });
        this.disabled = false;
        if (!res.ok) { window.aoToast(res.data.message || 'Foto tidak valid'); state.foto = null; document.getElementById('btn-retake').click(); return; }
        go(3);
    });

    // ---- STEP 3 ----
    function fillReview() {
        document.getElementById('rv-nik').textContent = document.getElementById('f-nik').value;
        document.getElementById('rv-nama').textContent = document.getElementById('f-nama').value;
        const jk = document.querySelector('input[name=jk]:checked');
        document.getElementById('rv-jk').textContent = jk ? (jk.value === 'L' ? 'Laki-Laki' : 'Perempuan') : '-';
        document.getElementById('rv-hp').textContent = document.getElementById('f-hp').value;
        if (state.foto) document.getElementById('rv-photo').src = state.foto;
        var rvt = document.getElementById('rv-tanggal');
        if (rvt) rvt.textContent = state.tanggalLabel || '-';
        buildFormSummary();
    }
    document.getElementById('agree').addEventListener('change', function () {
        document.getElementById('btn-step3').disabled = !this.checked;
    });
    document.getElementById('back-3').addEventListener('click', () => go(2));
    document.getElementById('btn-step3').addEventListener('click', () => document.getElementById('rwModal').classList.add('open'));
    document.getElementById('m-batal').addEventListener('click', () => document.getElementById('rwModal').classList.remove('open'));

    // ---- SUBMIT ----
    document.getElementById('m-ambil').addEventListener('click', async function () {
        this.disabled = true; this.textContent = 'Memproses…';
        const jkEl = document.querySelector('input[name=jk]:checked');
        const fd = new FormData();
        fd.append('setuju', document.getElementById('agree').checked ? '1' : '');
        fd.append('nik', document.getElementById('f-nik').value.trim());
        fd.append('nama', document.getElementById('f-nama').value.trim());
        fd.append('jk', jkEl ? jkEl.value : '');
        fd.append('no_hp', document.getElementById('f-hp').value.trim());
        fd.append('foto', state.foto || '');
        fd.append('tanggal', state.tanggal || '');
        fd.append('form_values', JSON.stringify(state.form || {}));
        const r = await fetch(ROUTES.submit, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: fd });
        const data = await r.json().catch(() => ({}));
        this.disabled = false; this.textContent = 'Ambil Antrian';
        document.getElementById('rwModal').classList.remove('open');
        if (!r.ok || !data.success) {
            window.aoToast(data.message || 'Gagal mengambil antrean');
            return;
        }
        // Render tiket + simpan di perangkat ini (tahan refresh/keluar, 10 menit)
        clearForm(); // isian form sudah tersimpan di server
        renderTicket(data);
        persistTicket(data);
        startExpiryCountdown(Date.now() + TICKET_TTL_MS);
        go(4);
        // Unduh otomatis sebagai bukti (jeda singkat agar QR selesai digambar dulu).
        // Hanya saat baru submit — bukan saat refresh/pulih, supaya tidak mengunduh berulang.
        setTimeout(downloadTicketPng, 400);
    });

    // Tombol unduh tiket sebagai gambar + tutup tiket / daftar baru
    var btnDownload = document.getElementById('btn-download');
    if (btnDownload) btnDownload.addEventListener('click', downloadTicketPng);
    var btnDaftarBaru = document.getElementById('btn-daftar-baru');
    if (btnDaftarBaru) btnDaftarBaru.addEventListener('click', function (e) {
        e.preventDefault();
        clearTicket();
        clearForm();
        if (expiryTimer) { clearInterval(expiryTimer); expiryTimer = null; }
        location.reload();
    });

    // Pulihkan tiket bila warga tak sengaja refresh/keluar (hanya perangkat ini)
    (function restoreTicket() {
        let saved = null;
        try { saved = JSON.parse(localStorage.getItem(LS_KEY) || 'null'); } catch (e) {}
        if (saved && saved.data && saved.exp && saved.exp > Date.now()) {
            renderTicket(saved.data);
            startExpiryCountdown(saved.exp);
            go(4);
        } else {
            clearTicket();
        }
    })();
})();
</script>
@endunless
@endpush
