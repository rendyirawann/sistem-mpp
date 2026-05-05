@foreach ($skpd as $item)
    <div class="col-6 col-md-4 col-xl-3">
        <div class="clean-card {{ !$item->is_layanan_buka ? 'opacity-50 pe-none' : '' }}"
            @if ($item->is_layanan_buka) onclick="openLayanan('{{ $item->id }}')" @endif>

            <div class="card-body p-8 d-flex flex-column align-items-center text-center position-relative h-100 justify-content-center">

                @if (!$item->is_layanan_buka)
                    <span class="badge bg-danger bg-opacity-10 text-danger position-absolute top-0 mt-4 px-3 py-2 fw-bold rounded-pill">
                        <i class="ph-fill ph-lock-key me-1"></i> {{ $item->pesan_tutup }}
                    </span>
                @endif

                <div class="icon-box" style="{{ !$item->is_layanan_buka ? 'background: #f3f4f6; color: #9ca3af;' : '' }}">
                    <i class="{{ $item->logo_skpd ? 'ph-fill ph-bank' : 'ph-fill ph-buildings' }}"></i>
                </div>

                <h3 class="card-title mb-0">
                    {{ $item->nama_skpd }}
                </h3>
                
                @if ($item->is_layanan_buka)
                    <div class="mt-4 w-100 opacity-0 transition-all card-hover-show position-absolute bottom-0 mb-6">
                        <span class="action-button primary py-2 px-4 shadow-sm fs-7">
                            Pilih <i class="ph ph-arrow-right fw-bold ms-1"></i>
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach

<style>
    .clean-card {
        position: relative;
        padding-bottom: 20px;
    }
    .clean-card:hover .card-hover-show {
        opacity: 1 !important;
        transform: translateY(-10px);
    }
    .card-hover-show {
        transform: translateY(0);
    }
</style>
