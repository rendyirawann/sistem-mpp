<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skpd;
use App\Models\Loket;
use App\Models\FormPersyaratan;

/**
 * Skema form persyaratan Dukcapil (F-1.02, F-1.03, F-1.06, F-2.01 Kelahiran & Kematian)
 * lengkap sesuai PDF, lalu ditautkan otomatis ke loket Capil sesuai pemetaan Excel.
 *
 * Struktur skema: { "sections": [ { "title": "...", "fields": [ {field}, ... ] } ] }
 * Field: key,label,type(text|number|date|time|select|radio|checkbox|textarea|file|repeater),
 *        required,rules,options[],columns[],showIf{field,in[]},default,inputmode,accept,maxKb
 */
class FormPersyaratanSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Helper builder field ----
        $t   = fn ($k, $l, $req = false, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'text', 'required' => $req], $e);
        $num = fn ($k, $l, $req = false, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'number', 'required' => $req], $e);
        $dt  = fn ($k, $l, $req = false, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'date', 'required' => $req], $e);
        $tm  = fn ($k, $l, $req = false, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'time', 'required' => $req], $e);
        $sel = fn ($k, $l, $o, $req = false, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'select', 'options' => $o, 'required' => $req], $e);
        $rad = fn ($k, $l, $o, $req = false, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'radio', 'options' => $o, 'required' => $req], $e);
        $chk = fn ($k, $l, $o, $req = false, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'checkbox', 'options' => $o, 'required' => $req], $e);
        $ta  = fn ($k, $l, $req = false, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'textarea', 'required' => $req], $e);
        $file = fn ($k, $l, $req = false, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'file', 'required' => $req], $e);
        $rep = fn ($k, $l, $cols, $e = []) => array_merge(['key' => $k, 'label' => $l, 'type' => 'repeater', 'columns' => $cols], $e);
        $sec = fn ($title, $fields, $note = null) => array_filter(['title' => $title, 'note' => $note, 'fields' => $fields]);

        $NIK  = ['rules' => 'digits:16', 'inputmode' => 'numeric', 'maxlength' => 16];
        $WN   = ['WNI', 'WNA'];
        $SHDK = ['Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu', 'Lainnya'];
        $PEND = ['Tidak/Belum Sekolah', 'Belum Tamat SD/Sederajat', 'Tamat SD/Sederajat', 'SLTP/Sederajat', 'SLTA/Sederajat', 'Diploma I/II', 'Akademi/Diploma III/S.Muda', 'Diploma IV/Strata I', 'Strata II', 'Strata III'];
        $AGM  = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Kepercayaan'];

        // ============================ F-1.02 ============================
        $f102 = ['sections' => [
            $sec('Data Pemohon', [
                $t('nama_lengkap', 'Nama Lengkap', true),
                $t('nik', 'Nomor Induk Kependudukan (NIK)', true, $NIK),
                $t('no_kk', 'Nomor Kartu Keluarga', true, $NIK),
            ]),
            $sec('Jenis Permohonan', [
                $rad('jenis_permohonan', 'Jenis Permohonan', ['Kartu Keluarga', 'KTP-el', 'Kartu Identitas Anak (KIA)', 'Perubahan Data'], true),
                $sel('kk_kategori', 'Kategori Kartu Keluarga', ['Baru - Membentuk Keluarga Baru', 'Baru - Penggantian Kepala Keluarga', 'Baru - Pisah KK', 'Baru - Pindah Datang', 'Baru - WNI dari Luar Negeri karena Pindah', 'Baru - Rentan Adminduk', 'Perubahan - Menumpang Dalam KK', 'Perubahan - Peristiwa Penting', 'Perubahan - Perubahan Elemen Data', 'Hilang', 'Rusak'], false, ['showIf' => ['field' => 'jenis_permohonan', 'in' => ['Kartu Keluarga']]]),
                $sel('ktp_kategori', 'Kategori KTP-el', ['Baru', 'Pindah Datang', 'Hilang', 'Rusak'], false, ['showIf' => ['field' => 'jenis_permohonan', 'in' => ['KTP-el']]]),
                $sel('kia_kategori', 'Kategori KIA', ['Baru', 'Hilang', 'Rusak'], false, ['showIf' => ['field' => 'jenis_permohonan', 'in' => ['Kartu Identitas Anak (KIA)']]]),
                $sel('perubahan_kategori', 'Perubahan Data', ['Kartu Keluarga', 'KTP-el', 'KIA'], false, ['showIf' => ['field' => 'jenis_permohonan', 'in' => ['Perubahan Data']]]),
            ]),
            $sec('Persyaratan yang Dilampirkan', [
                $chk('persyaratan', 'Dokumen yang dilampirkan', [
                    'KK Lama / KK Rusak', 'Buku Nikah / Kutipan Akta Perkawinan', 'Kutipan Akta Perceraian',
                    'Surat Keterangan Pindah', 'Surat Keterangan Pindah Luar Negeri', 'KTP-el Rusak',
                    'Dokumen Perjalanan', 'Surat Keterangan Hilang dari Kepolisian',
                    'Surat Keterangan/bukti perubahan Peristiwa Kependudukan & Peristiwa Penting',
                    'SPTJM Perkawinan/Perceraian belum tercatat', 'Akta Kematian',
                    'Surat Pernyataan penyebab hilang atau rusak', 'Surat Keterangan Pindah dari Perwakilan RI',
                    'Surat Pernyataan bersedia menerima sebagai anggota keluarga',
                    'Surat kuasa pengasuh anak dari orang tua/wali', 'Kartu Izin Tinggal Tetap',
                ]),
                $file('lampiran', 'Unggah Foto/Scan Dokumen Pendukung', false, ['accept' => 'image/*,application/pdf', 'maxKb' => 3072]),
            ], 'Beri centang pada dokumen yang Anda bawa. Petugas akan memverifikasi berkas fisik saat kedatangan.'),
        ]];

        // ============================ F-1.03 ============================
        $alamat = fn ($p, $label) => [
            $ta("{$p}_alamat", "$label — Alamat (Jalan/Dusun)"),
            $t("{$p}_rt", 'RT'), $t("{$p}_rw", 'RW'),
            ['key' => "{$p}_wilayah", 'label' => "$label — Wilayah (Provinsi → Desa)", 'type' => 'wilayah'],
            $t("{$p}_kodepos", 'Kode Pos'),
        ];
        $f103 = ['sections' => [
            $sec('Data Pemohon', [
                $t('no_kk', 'No. Kartu Keluarga', true, $NIK),
                $t('nama_lengkap', 'Nama Lengkap Pemohon', true),
                $t('nik', 'NIK', true, $NIK),
                $rad('jenis_permohonan', 'Jenis Permohonan', ['Surat Keterangan Pindah', 'Surat Keterangan Pindah Luar Negeri (SKPLN)', 'Surat Keterangan Tempat Tinggal (SKTT) Orang Asing'], true),
            ]),
            $sec('Alamat Asal', $alamat('asal', 'Asal')),
            $sec('Klasifikasi Kepindahan', [
                $rad('klasifikasi', 'Klasifikasi Kepindahan', [
                    'Dalam satu desa/kelurahan', 'Antar desa/kelurahan dalam satu kecamatan',
                    'Antar kecamatan dalam satu kabupaten/kota', 'Antar kabupaten/kota dalam satu provinsi', 'Antar provinsi',
                ], true),
            ]),
            $sec('Alamat Pindah (Tujuan)', $alamat('tujuan', 'Tujuan')),
            $sec('Alasan & Jenis Kepindahan', [
                $rad('alasan_pindah', 'Alasan Pindah', ['Pekerjaan', 'Pendidikan', 'Keamanan', 'Kesehatan', 'Perumahan', 'Keluarga', 'Lainnya'], true),
                $t('alasan_lainnya', 'Alasan Lainnya (sebutkan)', false, ['showIf' => ['field' => 'alasan_pindah', 'in' => ['Lainnya']]]),
                $rad('jenis_kepindahan', 'Jenis Kepindahan', ['Kepala Keluarga', 'Kepala Keluarga dan seluruh anggota keluarga', 'Kepala Keluarga dan sebagian anggota keluarga', 'Anggota Keluarga'], true),
                $rad('anggota_tidak_pindah', 'Anggota Keluarga Tidak Pindah', ['Numpang KK', 'Membuat KK Baru']),
                $rad('anggota_pindah_status', 'Anggota Keluarga Yang Pindah', ['Numpang KK', 'Membuat KK Baru']),
            ]),
            $sec('Daftar Anggota Keluarga yang Pindah', [
                $rep('anggota_pindah', 'Anggota Keluarga', [
                    ['key' => 'nik', 'label' => 'NIK', 'type' => 'text'],
                    ['key' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text'],
                    ['key' => 'shdk', 'label' => 'SHDK', 'type' => 'select', 'options' => $SHDK],
                ]),
            ]),
            $sec('Khusus Orang Asing (ITAS/ITAP)', [
                $t('sponsor_nama', 'Nama Sponsor'),
                $rad('sponsor_tipe', 'Tipe Sponsor', ['Organisasi Internasional', 'Pemerintah', 'Perusahaan', 'Perorangan', 'Tanpa Sponsor']),
                $ta('sponsor_alamat', 'Alamat Sponsor'),
                $t('kitas_nomor', 'Nomor KITAS/KITAP'),
                $dt('kitas_tanggal', 'Tanggal Masa Berlaku KITAS/KITAP'),
            ], 'Opsional — hanya untuk pemohon Orang Asing.'),
            $sec('Khusus Pindah Luar Negeri (SKPLN)', [
                $t('negara_tujuan', 'Negara Tujuan'),
                $t('kode_negara', 'Kode Negara'),
                $ta('alamat_tujuan_ln', 'Alamat Tujuan di Luar Negeri'),
                $t('penanggung_jawab', 'Penanggung Jawab'),
                $dt('rencana_pindah_tgl', 'Rencana Pindah Tanggal'),
            ], 'Opsional — hanya untuk permohonan SKPLN.'),
        ]];

        // ============================ F-1.06 ============================
        $f106 = ['sections' => [
            $sec('Data Pemohon', [
                $t('nama_lengkap', 'Nama Lengkap', true),
                $t('nik', 'NIK', true, $NIK),
                $t('no_kk', 'Nomor KK', true, $NIK),
                $ta('alamat_rumah', 'Alamat Rumah', true),
            ]),
            $sec('Rincian Anggota Kartu Keluarga', [
                $rep('rincian_kk', 'Anggota Keluarga', [
                    ['key' => 'nama', 'label' => 'Nama', 'type' => 'text'],
                    ['key' => 'nik', 'label' => 'NIK', 'type' => 'text'],
                    ['key' => 'shdk', 'label' => 'SHDK', 'type' => 'select', 'options' => $SHDK],
                    ['key' => 'keterangan', 'label' => 'Keterangan', 'type' => 'text'],
                ]),
            ]),
            $sec('Perubahan Pendidikan & Pekerjaan', [
                $rep('perubahan_pendidikan_pekerjaan', 'Baris Perubahan', [
                    ['key' => 'pendidikan_semula', 'label' => 'Pendidikan Semula', 'type' => 'select', 'options' => $PEND],
                    ['key' => 'pendidikan_menjadi', 'label' => 'Pendidikan Menjadi', 'type' => 'select', 'options' => $PEND],
                    ['key' => 'pendidikan_dasar', 'label' => 'Dasar Perubahan', 'type' => 'text'],
                    ['key' => 'pekerjaan_semula', 'label' => 'Pekerjaan Semula', 'type' => 'text'],
                    ['key' => 'pekerjaan_menjadi', 'label' => 'Pekerjaan Menjadi', 'type' => 'text'],
                    ['key' => 'pekerjaan_dasar', 'label' => 'Dasar Perubahan', 'type' => 'text'],
                ]),
            ]),
            $sec('Perubahan Agama & Lainnya', [
                $rep('perubahan_agama_lain', 'Baris Perubahan', [
                    ['key' => 'agama_semula', 'label' => 'Agama Semula', 'type' => 'select', 'options' => $AGM],
                    ['key' => 'agama_menjadi', 'label' => 'Agama Menjadi', 'type' => 'select', 'options' => $AGM],
                    ['key' => 'agama_dasar', 'label' => 'Dasar Perubahan', 'type' => 'text'],
                    ['key' => 'lainnya_semula', 'label' => 'Lainnya (Semula)', 'type' => 'text'],
                    ['key' => 'lainnya_menjadi', 'label' => 'Lainnya (Menjadi)', 'type' => 'text'],
                    ['key' => 'lainnya_dasar', 'label' => 'Dasar Perubahan', 'type' => 'text'],
                ]),
            ]),
        ]];

        // ==================== F-2.01 KELAHIRAN ====================
        $pelapor = fn () => [
            $t('pelapor_nama', 'Nama', true),
            $t('pelapor_nik', 'NIK', true, $NIK),
            $t('pelapor_dok_perjalanan', 'Nomor Dokumen Perjalanan'),
            $t('pelapor_no_kk', 'Nomor Kartu Keluarga', true, $NIK),
            $rad('pelapor_wn', 'Kewarganegaraan', $WN),
        ];
        $saksi = fn ($n) => [
            $t("saksi{$n}_nama", 'Nama', true),
            $t("saksi{$n}_nik", 'NIK', true, $NIK),
            $t("saksi{$n}_no_kk", 'Nomor Kartu Keluarga'),
            $rad("saksi{$n}_wn", 'Kewarganegaraan', $WN),
        ];
        $f201lahir = ['sections' => [
            $sec('Wilayah Peristiwa', [
                ['key' => 'wilayah', 'label' => 'Wilayah Peristiwa (Provinsi → Desa)', 'type' => 'wilayah'],
            ]),
            $sec('Data Pelapor', $pelapor()),
            $sec('Data Saksi I', $saksi(1)),
            $sec('Data Saksi II', $saksi(2)),
            $sec('Data Orang Tua', [
                $t('ayah_nama', 'Nama Ayah', true),
                $t('ayah_nik', 'NIK Ayah', true, $NIK),
                $t('ayah_tempat_lahir', 'Tempat Lahir Ayah'),
                $dt('ayah_tgl_lahir', 'Tanggal Lahir Ayah'),
                $rad('ayah_wn', 'Kewarganegaraan Ayah', $WN),
                $t('ibu_nama', 'Nama Ibu', true),
                $t('ibu_nik', 'NIK Ibu', true, $NIK),
                $t('ibu_tempat_lahir', 'Tempat Lahir Ibu'),
                $dt('ibu_tgl_lahir', 'Tanggal Lahir Ibu'),
                $rad('ibu_wn', 'Kewarganegaraan Ibu', $WN),
            ]),
            $sec('Data Anak', [
                $t('anak_nama', 'Nama Anak', true),
                $rad('anak_jk', 'Jenis Kelamin', ['Laki-laki', 'Perempuan'], true),
                $rad('anak_tempat_dilahirkan', 'Tempat Dilahirkan', ['RS/RB', 'Puskesmas', 'Polindes', 'Rumah', 'Lainnya'], true),
                $t('anak_tempat_kelahiran', 'Tempat Kelahiran (Kota/Kabupaten)', true),
                $dt('anak_tgl_lahir', 'Hari & Tanggal Lahir', true),
                $tm('anak_pukul', 'Pukul'),
                $rad('anak_jenis_kelahiran', 'Jenis Kelahiran', ['Tunggal', 'Kembar 2', 'Kembar 3', 'Kembar 4'], true),
                $num('anak_kelahiran_ke', 'Kelahiran ke- (dalam satu perkawinan)'),
                $rad('anak_penolong', 'Penolong Kelahiran', ['Dokter', 'Bidan/Perawat', 'Dukun', 'Lainnya']),
                $num('anak_berat', 'Berat Bayi (gram)'),
                $num('anak_panjang', 'Panjang Bayi (cm)'),
            ]),
        ]];

        // ==================== F-2.01 KEMATIAN ====================
        $f201mati = ['sections' => [
            $sec('Wilayah Peristiwa', [
                ['key' => 'wilayah', 'label' => 'Wilayah Peristiwa (Provinsi → Desa)', 'type' => 'wilayah'],
            ]),
            $sec('Data Pelapor', $pelapor()),
            $sec('Data Saksi I', $saksi(1)),
            $sec('Data Saksi II', $saksi(2)),
            $sec('Data Orang Tua', [
                $t('ayah_nama', 'Nama Ayah', true),
                $t('ayah_nik', 'NIK Ayah', false, $NIK),
                $t('ayah_tempat_lahir', 'Tempat Lahir Ayah'),
                $dt('ayah_tgl_lahir', 'Tanggal Lahir Ayah'),
                $t('ibu_nama', 'Nama Ibu', true),
                $t('ibu_nik', 'NIK Ibu', false, $NIK),
                $t('ibu_tempat_lahir', 'Tempat Lahir Ibu'),
                $dt('ibu_tgl_lahir', 'Tanggal Lahir Ibu'),
            ]),
            $sec('Data Kematian', [
                $t('mati_nik', 'NIK Almarhum/ah', true, $NIK),
                $t('mati_nama', 'Nama Lengkap Almarhum/ah', true),
                $dt('mati_tgl', 'Tanggal Kematian', true),
                $tm('mati_pukul', 'Pukul'),
                $rad('mati_sebab', 'Sebab Kematian', ['Sakit biasa/Tua', 'Wabah Penyakit', 'Kecelakaan', 'Kriminalitas', 'Bunuh Diri', 'Lainnya'], true),
                $t('mati_tempat', 'Tempat Kematian', true),
                $rad('mati_yang_menerangkan', 'Yang Menerangkan', ['Dokter', 'Tenaga Kesehatan', 'Kepolisian', 'Lainnya']),
            ]),
        ]];

        // ---- Simpan template (idempotent by kode) ----
        $templates = [
            ['kode' => 'F-1.02', 'nama' => 'Formulir Pendaftaran Peristiwa Kependudukan', 'deskripsi' => 'F-1.02 — KK / KTP-el / KIA / Perubahan Data', 'skema' => $f102],
            ['kode' => 'F-1.03', 'nama' => 'Formulir Pendaftaran Perpindahan Penduduk', 'deskripsi' => 'F-1.03 — Surat Keterangan Pindah', 'skema' => $f103],
            ['kode' => 'F-1.06', 'nama' => 'Surat Pernyataan Perubahan Elemen Data Kependudukan', 'deskripsi' => 'F-1.06 — Perubahan elemen data', 'skema' => $f106],
            ['kode' => 'F-2.01-LAHIR', 'nama' => 'Formulir Pelaporan Pencatatan Sipil Kelahiran', 'deskripsi' => 'F-2.01 — Akta Kelahiran', 'skema' => $f201lahir],
            ['kode' => 'F-2.01-MATI', 'nama' => 'Formulir Pelaporan Pencatatan Sipil Kematian', 'deskripsi' => 'F-2.01 — Akta Kematian', 'skema' => $f201mati],
        ];

        $saved = [];
        foreach ($templates as $tpl) {
            $form = FormPersyaratan::updateOrCreate(
                ['kode' => $tpl['kode']],
                ['nama' => $tpl['nama'], 'deskripsi' => $tpl['deskripsi'], 'skema' => $tpl['skema'], 'is_active' => true]
            );
            $saved[$tpl['kode']] = $form;
        }

        // ---- Tautkan ke loket Capil (best-effort by nama_loket) ----
        // Resolve dulu id SKPD Capil (hindari join lintas-collation).
        $capilIds = Skpd::where('nama_skpd', 'like', '%Catatan Sipil%')
            ->orWhere('nama_skpd', 'like', '%Pencatatan Sipil%')
            ->orWhere('nama_skpd', 'like', '%Kependudukan%')
            ->pluck('id')->all();

        // pola nama loket => [kode form => urutan]
        $map = [
            'Pencetakan KTP'          => ['F-1.02' => 0],
            'Kartu Identitas Anak'    => ['F-1.02' => 0],
            'Kartu Keluarga'          => ['F-1.02' => 0, 'F-1.06' => 1],
            'Pindah'                  => ['F-1.03' => 0],
            'Akta Kelahiran'          => ['F-2.01-LAHIR' => 0],
            'Akta Kematian'           => ['F-2.01-MATI' => 0],
        ];

        $attached = 0;
        if (!empty($capilIds)) {
            foreach ($map as $pola => $forms) {
                $lokets = Loket::whereIn('skpd_id', $capilIds)
                    ->where('nama_loket', 'like', "%{$pola}%")
                    ->get();
                foreach ($lokets as $loket) {
                    $sync = [];
                    foreach ($forms as $kode => $urutan) {
                        if (isset($saved[$kode])) {
                            $sync[$saved[$kode]->id] = ['urutan' => $urutan];
                        }
                    }
                    if ($sync) {
                        $loket->formPersyaratan()->syncWithoutDetaching($sync);
                        $attached++;
                    }
                }
            }
        }

        $this->command?->info("Form persyaratan: " . count($saved) . " template disimpan, {$attached} loket ditautkan.");
    }
}
