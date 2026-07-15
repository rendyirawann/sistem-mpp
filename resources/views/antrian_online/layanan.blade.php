@extends('antrian_online.layout')
@section('title', 'Layanan ' . $tenant->nama_skpd)

@section('content')
    <section class="ao-section" style="margin-top:0;padding-top:130px;">
        <div class="ao-container">
            <div class="ao-panel">
                <div class="ao-panel-head">
                    <div class="ao-panel-title">
                        <a href="{{ route('antrian-online.list') }}" class="ao-ic" aria-label="Kembali" style="text-decoration:none;">
                            <i class="ph ph-arrow-left"></i>
                        </a>
                        Layanan {{ $tenant->nama_skpd }}
                    </div>
                </div>

                <div class="ao-grid">
                    @forelse ($lokets as $loket)
                        <div class="ao-card">
                            <div class="ao-svc-icon">
                                <i class="ph ph-headset"></i>
                            </div>
                            <div class="ao-card-name ao-svc-title">{{ $loket->nama_loket }}</div>
                            <div class="ao-card-sub">Kode: {{ $loket->kode_tenant ?? '-' }} &middot; Prefix {{ $loket->prefix_tenant ?? '-' }}</div>
                            <a href="{{ route('antrian-online.registrasi', $loket->id) }}" class="ao-card-btn" style="text-decoration:none;">
                                Pilih Layanan
                            </a>
                        </div>
                    @empty
                        <div style="grid-column:1/-1;text-align:center;color:var(--ao-muted);padding:30px;">
                            Belum ada layanan aktif pada instansi ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
