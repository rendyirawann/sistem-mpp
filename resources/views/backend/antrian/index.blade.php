@extends('backend.layout.app')
@section('title', 'Panggilan Antrian')

@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-4 py-lg-6">
    <div class="d-flex flex-stack flex-wrap gap-4">
        <div class="page-title">
            <h1 class="page-heading fw-bold fs-3">Panggilan Antrian</h1>
            <ul class="breadcrumb fw-semibold fs-7">
                <li class="breadcrumb-item text-muted">Home</li>
                <li class="breadcrumb-item text-muted">Antrian</li>
                <li class="breadcrumb-item text-gray-900">Panggilan</li>
            </ul>
        </div>
    </div>
</div>
<!-- Informasi -->
<div id="kt_app_content" class="app-content flex-column-fluid">
<div class="row g-5 mb-5">

    @php
        $cards = [
            ['id'=>'jumlah-antrian','title'=>'Jumlah Antrian','icon'=>'ki-users','color'=>'warning'],
            ['id'=>'antrian-sekarang','title'=>'Antrian Sekarang','icon'=>'ki-user-check','color'=>'success'],
            ['id'=>'antrian-selanjutnya','title'=>'Antrian Selanjutnya','icon'=>'ki-user-plus','color'=>'info'],
            ['id'=>'sisa-antrian','title'=>'Sisa Antrian','icon'=>'ki-user','color'=>'danger'],
        ];
    @endphp

    @foreach($cards as $c)
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="symbol symbol-45px me-4">
                    <span class="symbol-label bg-light-{{ $c['color'] }}">
                        <i class="ki-outline {{ $c['icon'] }} fs-2 text-{{ $c['color'] }}"></i>
                    </span>
                </div>
                <div>
                    <div id="{{ $c['id'] }}" class="fs-2 fw-bold text-{{ $c['color'] }}"></div>
                    <div class="fw-semibold text-gray-500">{{ $c['title'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>
<!-- Table -->
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title">Daftar Antrian</h3>
    </div>

    <div class="card-body">
        <table id="tabel-antrian" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-muted fw-bold text-uppercase">
                    <th class="text-center">No Antrian</th>
                    <th>Status</th>
                    <th class="text-center">Loket</th>
                    <th class="text-center">Panggil</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
</div>
@endsection

<audio id="tingtung" src="{{ asset('assets/audio/tingtung.mp3') }}"></audio>
@push('scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="https://code.responsivevoice.org/responsivevoice.js?key=jQZ2zcdq"></script>

<script>
$(document).ready(function () {

    function loadInfo(){
    $('#jumlah-antrian').load("{{ route('antrian.jumlah') }}");
    $('#antrian-sekarang').load("{{ route('antrian.sekarang') }}");
    $('#antrian-selanjutnya').load("{{ route('antrian.selanjutnya') }}");
    $('#sisa-antrian').load("{{ route('antrian.sisa') }}");
    }

    let table = $('#tabel-antrian').DataTable({
        processing:true,
        serverSide:true,
        searching:false,
        ordering:false,
        ajax:"{{ route('antrian.get') }}",
        columns:[
            {data:'no_antrian', className:'text-center'},
            {data:'status', visible:false},
            {data:'loket', className:'text-center'},
            {
                data:null,
                className:'text-center',
                render:function(d){
                    if(d.status=="0")
                        return `<button class="btn btn-success btn-sm btn-call">
                                    <i class="ki-outline ki-microphone"></i>
                                </button>`;
                    if(d.status=="1")
                        return `<button class="btn btn-secondary btn-sm btn-call">
                                    <i class="ki-outline ki-microphone"></i>
                                </button>`;
                    return '-';
                }
            }
        ]
    });

    $('#tabel-antrian').on('click','.btn-call',function(){
    let data = table.row($(this).parents('tr')).data();
    let bell = document.getElementById('tingtung');

    bell.pause();
    bell.currentTime = 0;
    bell.play();

    setTimeout(()=>{
        responsiveVoice.speak(
            "Nomor Antrian " + data.no_antrian + " menuju loket " + data.loket,
            "Indonesian Male",
            {rate:0.9,pitch:1}
        );
    }, bell.duration * 770);

    $.post("{{ route('antrian.update') }}",{
        _token:"{{ csrf_token() }}",
        id:data.id
    });
});
setInterval(function(){
    loadInfo();
    table.ajax.reload(null,false);
},1000);
})
</script>
@endpush
