@php
    $logoDefault = asset('assets/media/logos/logo_deliserdang.png');
    $logo = $tenant->logo_skpd
        ? asset('storage/user/logo_skpd/' . $tenant->logo_skpd)
        : $logoDefault;
    $online = (bool) $tenant->is_antrianonline;
    $layanan = $tenant->lokets->pluck('nama_loket')->filter()->values();
    $layananCount = $layanan->count();
@endphp
<div class="ao-card">
    <div class="ao-card-top">
        <div class="ao-card-logo">
            <img src="{{ $logo }}" alt="Logo {{ $tenant->nama_skpd }}"
                onerror="this.onerror=null;this.src='{{ $logoDefault }}';">
        </div>
        @if ($layananCount)
            <span class="ao-card-count" title="{{ $layananCount }} layanan tersedia">
                <i class="ph ph-list-dashes"></i>
                {{ $layananCount }} Layanan
            </span>
            <button type="button" class="ao-info"
                data-tenant="{{ $tenant->nama_skpd }}"
                data-layanan="{{ json_encode($layanan) }}"
                aria-label="Lihat daftar layanan {{ $tenant->nama_skpd }}" title="Lihat daftar layanan">
                <i class="ph ph-info"></i>
            </button>
        @endif
    </div>
    <div class="ao-card-name">{{ $tenant->nama_skpd }}</div>
    @if ($online)
        <a href="{{ route('antrian-online.layanan', $tenant->id) }}" class="ao-card-btn">Pilih Tenan</a>
    @else
        <button type="button" class="ao-card-btn ao-soon" disabled aria-disabled="true">Available Soon</button>
    @endif
</div>
