@foreach ($skpd as $item)
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card card-flush h-100 border-0 shadow-sm card-service {{ !$item->is_layanan_buka ? 'opacity-50 bg-secondary pe-none' : 'cursor-pointer' }}"
            @if ($item->is_layanan_buka) onclick="openLayanan('{{ $item->id }}')" @endif>

            <div
                class="card-body d-flex flex-column justify-content-center align-items-center text-center p-6 position-relative">

                @if (!$item->is_layanan_buka)
                    <span class="badge badge-danger position-absolute top-0 mt-3 px-3 py-2 fw-bolder shadow-sm">
                        <i class="fa fa-lock text-white me-1"></i> {{ $item->pesan_tutup }}
                    </span>
                @endif

                <div
                    class="symbol symbol-60px symbol-circle {{ !$item->is_layanan_buka ? 'bg-light-dark' : 'bg-light-primary' }} mb-5 mt-4 d-flex justify-content-center align-items-center transition-all">
                    <div
                        class="symbol-label fs-2hx fw-bold {{ !$item->is_layanan_buka ? 'text-dark' : 'text-primary' }} bg-transparent">
                        <i
                            class="fa {{ $item->logo_skpd ? 'fa-building-columns' : 'fa-building' }} fs-1 {{ !$item->is_layanan_buka ? 'text-gray-600' : 'text-primary' }}"></i>
                    </div>
                </div>
                <h3 class="text-gray-800 fw-bolder fs-6 mb-0 lh-sm line-clamp-2 px-2">
                    {{ $item->nama_skpd }}
                </h3>
            </div>
        </div>
    </div>
@endforeach
