<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

/**
 * Sinkron data wilayah (provinsi/kabupaten/kecamatan/desa) dari dataset standar
 * Kemendagri (kode BPS — sama dengan yang dipakai OSS). Upsert by id sehingga
 * id tetap stabil (relasi existing aman) & selaras dengan struktur tabel.
 *
 * Contoh:
 *   php artisan wilayah:sync                       # prov + kab + kec (semua)
 *   php artisan wilayah:sync --desa --kabupaten=1212   # + desa Deli Serdang
 *   php artisan wilayah:sync --desa --provinsi=12      # + desa se-Sumut
 *   php artisan wilayah:sync --desa                    # + desa seluruh Indonesia (berat)
 */
class SyncWilayah extends Command
{
    protected $signature = 'wilayah:sync {--desa : ikut sinkron level desa (berat)} {--provinsi= : batasi kode provinsi} {--kabupaten= : batasi kode kabupaten}';
    protected $description = 'Sinkron data wilayah (provinsi, kabupaten, kecamatan, desa) dari dataset Kemendagri/BPS.';

    private string $base = 'https://www.emsifa.com/api-wilayah-indonesia/api/';

    public function handle(): int
    {
        $filterProv = $this->option('provinsi');
        $filterKab  = $this->option('kabupaten');
        $withDesa   = (bool) $this->option('desa');

        // ---------- PROVINSI ----------
        $prov = $this->get('provinces.json');
        if ($prov === null) { $this->error('Gagal mengambil data provinsi.'); return self::FAILURE; }
        $rows = [];
        foreach ($prov as $p) {
            if ($filterProv && (string) $p['id'] !== (string) $filterProv) continue;
            if ($filterKab && substr((string) $filterKab, 0, 2) !== (string) $p['id']) continue;
            $rows[] = $this->row(['id' => $p['id'], 'nama' => $p['name']]);
        }
        $this->upsert('wilayah_provinsi', $rows, ['nama']);
        $this->info('Provinsi: ' . count($rows) . ' disinkron.');
        $provIds = array_column($rows, 'id');

        // ---------- KABUPATEN ----------
        $kabIds = [];
        foreach ($provIds as $pid) {
            $kab = $this->get("regencies/{$pid}.json");
            if ($kab === null) continue;
            $rows = [];
            foreach ($kab as $k) {
                if ($filterKab && (string) $k['id'] !== (string) $filterKab) continue;
                $rows[] = $this->row(['id' => $k['id'], 'wilayah_provinsi_id' => $pid, 'nama' => $k['name']]);
                $kabIds[] = $k['id'];
            }
            $this->upsert('wilayah_kabupaten', $rows, ['nama', 'wilayah_provinsi_id']);
        }
        $this->info('Kabupaten: ' . count($kabIds) . ' disinkron.');

        // ---------- KECAMATAN ----------
        $kecIds = [];
        $barKec = $this->output->createProgressBar(count($kabIds));
        $barKec->start();
        foreach ($kabIds as $kid) {
            $kec = $this->get("districts/{$kid}.json");
            if ($kec !== null) {
                $rows = [];
                foreach ($kec as $c) {
                    $rows[] = $this->row(['id' => $c['id'], 'wilayah_kabupaten_id' => $kid, 'nama' => trim($c['name'])]);
                    $kecIds[] = $c['id'];
                }
                $this->upsert('wilayah_kecamatan', $rows, ['nama', 'wilayah_kabupaten_id']);
            }
            $barKec->advance();
        }
        $barKec->finish();
        $this->newLine();
        $this->info('Kecamatan: ' . count($kecIds) . ' disinkron.');

        // ---------- DESA (opsional, berat) ----------
        if ($withDesa) {
            $total = 0;
            $barDesa = $this->output->createProgressBar(count($kecIds));
            $barDesa->start();
            foreach ($kecIds as $cid) {
                $desa = $this->get("villages/{$cid}.json");
                if ($desa !== null && count($desa)) {
                    $rows = [];
                    foreach ($desa as $d) {
                        $rows[] = $this->row(['id' => $d['id'], 'wilayah_kecamatan_id' => $cid, 'nama' => trim($d['name'])]);
                    }
                    $this->upsert('wilayah_desa', $rows, ['nama', 'wilayah_kecamatan_id']);
                    $total += count($rows);
                }
                $barDesa->advance();
            }
            $barDesa->finish();
            $this->newLine();
            $this->info('Desa: ' . $total . ' disinkron.');
        } else {
            $this->line('Level desa dilewati (jalankan dengan --desa untuk menyertakan).');
        }

        return self::SUCCESS;
    }

    private function get(string $path): ?array
    {
        try {
            $r = Http::withoutVerifying()->timeout(30)->retry(3, 800)->get($this->base . $path);
            return $r->successful() && is_array($r->json()) ? $r->json() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function row(array $attrs): array
    {
        $now = Carbon::now();
        return array_merge($attrs, ['created_at' => $now, 'updated_at' => $now]);
    }

    private function upsert(string $table, array $rows, array $update): void
    {
        if (empty($rows)) return;
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table($table)->upsert($chunk, ['id'], array_merge($update, ['updated_at']));
        }
    }
}
