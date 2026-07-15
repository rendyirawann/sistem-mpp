@extends('antrian_online.layout')
@section('title', 'Beranda')

@section('content')
    {{-- ============ HERO ============ --}}
    <section class="ao-hero">
        <div class="ao-container">
            <div class="ao-hero-badge">
                <i class="ph-fill ph-shield-check"></i>
                Resmi &amp; Tanpa Biaya
            </div>
            <h1>{{ $settings['hero_title'] ?? 'Layanan Antrean Online Mandiri' }}</h1>
            <p>{{ $settings['hero_subtitle'] ?? '' }}</p>
            @php $hl = $settings['hero_button_link'] ?? '#'; @endphp
            <a href="#ao-tenants" class="ao-btn ao-btn-light" style="margin-right:10px;">
                Ambil Antrean
                <i class="ph ph-arrow-down"></i>
            </a>
            <a href="{{ $hl }}" class="ao-btn ao-btn-dark"
                target="{{ \Illuminate\Support\Str::startsWith($hl, 'http') ? '_blank' : '_self' }}"
                @if (\Illuminate\Support\Str::startsWith($hl, 'http')) rel="noopener noreferrer" @endif>
                {{ $settings['hero_button_label'] ?? 'Panduan' }}
                <i class="ph ph-arrow-right"></i>
            </a>
        </div>
    </section>

    {{-- ============ TENANT SECTION ============ --}}
    <section class="ao-section" id="ao-tenants">
        <div class="ao-container">
            <div class="ao-panel">
                <div class="ao-panel-head">
                    <div class="ao-panel-title">
                        <span class="ao-ic"><i class="ph ph-buildings"></i></span>
                        Tenan
                    </div>
                    <a href="{{ route('antrian-online.list') }}" class="ao-seeall">
                        Lihat Semua
                        <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <div class="ao-grid">
                    @forelse ($tenants as $tenant)
                        @include('antrian_online.partials.tenant_card')
                    @empty
                        <div style="grid-column:1/-1;text-align:center;color:var(--ao-muted);padding:30px;">
                            Belum ada tenant yang tersedia.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
