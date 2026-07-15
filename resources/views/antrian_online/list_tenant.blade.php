@extends('antrian_online.layout')
@section('title', 'Seluruh Layanan Tenan')

@section('content')
    <section class="ao-section" style="margin-top:0;padding-top:130px;">
        <div class="ao-container">
            <div class="ao-panel">
                <div class="ao-panel-head">
                    <div class="ao-panel-title">
                        <span class="ao-ic"><i class="ph ph-buildings"></i></span>
                        Seluruh Layanan Tenan
                    </div>
                    <a href="{{ route('antrian-online') }}" class="ao-seeall">
                        <i class="ph ph-arrow-left"></i>
                        Kembali
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
