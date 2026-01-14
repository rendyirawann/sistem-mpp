@extends('backend.layout.app')
@section('title', 'Dashboard')
@section('content')

    <div id="kt_app_toolbar" class="app-toolbar  d-flex flex-stack py-4 py-lg-8">
        <div class="d-flex flex-grow-1 flex-stack flex-wrap gap-2 mb-n10" id="kt_toolbar">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 ">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Dashboard
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a class="text-muted text-hover-primary">Home</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">Dashboard</li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-gray-900">Overview</li>
                </ul>
            </div>
            <div class="d-flex align-items-center pt-4 pb-7 pt-lg-1 pb-lg-2">
                <div class="me-3">...</div>
                <a href="#" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal"
                    data-bs-target="#kt_modal_create_app" id="kt_toolbar_primary_button">
                    Create </a>
            </div>
        </div>
    </div>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card border border-gray-300">
                    <div class="card-header border-gray-300">
                        <h3 class="card-title">Peta Nasional Republik Indonesia</h3>
                    </div>
                    <div class="card-body">
                        <button id="back-button" class="btn btn-sm btn-primary mb-3" style="display:none;">
                            ← Kembali ke Peta Nasional
                        </button>
                        <div id="map-container" style="height: 600px;  overflow: hidden;">
                            <svg id="indonesia-map" width="100%" height="100%"></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://d3js.org/d3.v7.min.js"></script>

        <script>
            let ALL_REGENCY_DATA = null;
            let ALL_PROVINCE_DATA = null;

            // URL GeoJSON (sesuaikan jika path berbeda)
            const PROVINCE_GEOJSON_URL = '{{ asset('data/38 Provinsi Indonesia - Provinsi.json') }}';
            const REGENCY_GEOJSON_URL = '{{ asset('data/38 Provinsi Indonesia - Kabupaten.json') }}';

            const WIDTH = 1440;
            const HEIGHT = 600;

            let svg, path, projection;
            let activeFeature = null;

            // kandidat properti (sesuaikan bila perlu)
            const PROV_NAME_KEYS = ['NAMA_PROV', 'NAME_1', 'provinsi', 'PROVINSI', 'nama', 'NAME'];
            const PROV_CODE_KEYS = ['KODE_PROV', 'ID_PROV', 'PROV', 'id', 'kode', 'PROVINCE_ID', 'prov_id'];
            const KAB_NAME_KEYS = [
                'NAMOBJ', // nama objek (sering berisi nama desa/kelurahan)
                'WADMKD', // nama desa/kelurahan
                'WADMKC', // nama kecamatan
                'WADMKK', // nama kabupaten/kota
                'WADMPR', // nama provinsi (kadang ada)
                'NAME', 'name', 'nama', 'NAMA_KAB', 'NAME_2', 'kabupaten'
            ];
            const KAB_CODE_KEYS = [
                'KDPKAB', 'KDPBPS', 'KDPUM', 'KDPKAB', 'KDPBPS', 'KDPUM', 'KDPBPS', // berbagai kode
                'KDPKAB', 'KDPKAB',
                'KDPKAB', 'KDPKAB'
                // (tambahkan kunci lain jika dataset kamu punya)
            ];

            const kabProvKeys = [
                'WADMPR', 'PROVINSI', // nama provinsi (WADMPR di dataset kabupaten)
                'KDPKAB', 'KDPPUM', // kode kabupaten (biasanya "33.72" => prefix = kode prov)
                'PROVINCE_ID', 'NAME_1', // alternatif
                'WADMKK', 'WADMPR'
            ];

            // -------------------- helpers --------------------
            function getProp(props, candidates) {
                if (!props) return '';
                for (const k of candidates) {
                    if (Object.prototype.hasOwnProperty.call(props, k) && props[k] != null && props[k] !== '') return props[k];
                }
                return '';
            }

            function normalize(v) {
                if (v === null || v === undefined) return '';
                return String(v).toLowerCase().trim().replace(/\s+/g, ' ');
            }

            // -------------------- init --------------------
            document.addEventListener('DOMContentLoaded', () => {
                svg = d3.select('#indonesia-map')
                    .attr('viewBox', `0 0 ${WIDTH} ${HEIGHT}`);

                // tooltip
                createTooltip();

                // load files
                Promise.all([d3.json(PROVINCE_GEOJSON_URL), d3.json(REGENCY_GEOJSON_URL)])
                    .then(([prov, kab]) => {
                        ALL_PROVINCE_DATA = prov;
                        ALL_REGENCY_DATA = kab;
                        loadMap(ALL_PROVINCE_DATA, false);
                    })
                    .catch(err => {
                        console.error('Gagal memuat GeoJSON:', err);
                        alert('Gagal memuat GeoJSON. Periksa path file di public/data/');
                    });

                // back button (harus ada elemen #back-button di blade, atau buat sendiri)
                const backBtn = document.getElementById('back-button');
                if (backBtn) backBtn.addEventListener('click', resetMap);
            });

            // -------------------- render --------------------
            function loadMap(geojson, isRegency = false) {
                svg.selectAll('*').remove();

                if (!geojson || !Array.isArray(geojson.features) || geojson.features.length === 0) {
                    svg.append('text')
                        .attr('x', WIDTH / 2).attr('y', HEIGHT / 2)
                        .attr('text-anchor', 'middle')
                        .attr('fill', '#333')
                        .text('Tidak ada fitur untuk ditampilkan');
                    return;
                }

                // gunakan geoIdentity untuk data ini (sesuai debug sebelumnya), fit ke ukuran
                projection = d3.geoIdentity().reflectY(true);
                try {
                    projection.fitSize([WIDTH, HEIGHT], geojson);
                } catch (e) {
                    console.warn('fitSize gagal:', e);
                }
                path = d3.geoPath().projection(projection);

                const g = svg.append('g').attr('class', 'map-layer');

                // color / style
                const color = isRegency ? '#FFA500' : '#1E90FF';
                const stroke = '#ffffff';
                const strokeWidth = isRegency ? 0.5 : 0.8;

                // draw features
                g.selectAll('path')
                    .data(geojson.features)
                    .enter()
                    .append('path')
                    .attr('d', path)
                    .attr('fill-rule', 'evenodd') // safer for holes
                    .style('fill', color)
                    .style('fill-opacity', 0.95)
                    .style('stroke', stroke)
                    .style('stroke-width', strokeWidth)
                    .style('cursor', 'pointer')
                    .on('mouseover', function(event, d) {
                        d3.select(this).raise()
                            .transition().duration(120)
                            .style('fill-opacity', 1)
                            .style('stroke-width', strokeWidth + 0.8);
                        const label = getProp(d.properties, isRegency ? KAB_NAME_KEYS : PROV_NAME_KEYS) ||
                            '(nama tidak tersedia)';
                        showTooltip(event, label);
                    })
                    .on('mousemove', function(event) {
                        moveTooltip(event);
                    })
                    .on('mouseout', function(event) {
                        d3.select(this)
                            .transition().duration(120)
                            .style('fill-opacity', 0.95)
                            .style('stroke-width', strokeWidth);
                        hideTooltip();
                    })
                    .on('click', function(event, d) {
                        if (!isRegency) {
                            activeFeature = d;
                            zoomToFeature(d);
                            const kodeProv = getProp(d.properties, PROV_CODE_KEYS) || getProp(d.properties, PROV_NAME_KEYS);
                            loadRegencies(kodeProv, d);
                        } else {
                            const nama = getProp(d.properties, KAB_NAME_KEYS) || getProp(d.properties, KAB_CODE_KEYS) ||
                                'Kabupaten';
                            const kode = getProp(d.properties, KAB_CODE_KEYS) || '';
                            // ganti alert dengan sidebar/modal jika perlu
                            alert(`${nama}\nKode: ${kode}`);
                        }
                    });

                // optional: draw bounding box for debug (comment out in production)
                // try { const b = path.bounds(geojson); svg.append('rect').attr('x',b[0][0]).attr('y',b[0][1]).attr('width',b[1][0]-b[0][0]).attr('height',b[1][1]-b[0][1]).style('fill','none').style('stroke','red').style('stroke-dasharray','4 4'); } catch(e){}
            }

            // -------------------- zoom --------------------
            function zoomToFeature(d) {
                if (!path) return;
                const b = path.bounds(d);
                const dx = b[1][0] - b[0][0];
                const dy = b[1][1] - b[0][1];
                const x = (b[0][0] + b[1][0]) / 2;
                const y = (b[0][1] + b[1][1]) / 2;
                const scale = Math.max(1, Math.min(8, 0.9 / Math.max(dx / WIDTH, dy / HEIGHT)));
                const translate = [WIDTH / 2 - scale * x, HEIGHT / 2 - scale * y];

                svg.select('g')
                    .transition().duration(700)
                    .attr('transform', `translate(${translate}) scale(${scale})`);
            }

            // -------------------- filter kabupaten --------------------
            function loadRegencies(kodeProvinsiCandidate, provFeature) {
                if (!ALL_REGENCY_DATA) return;

                const provCodeNorm = normalize(kodeProvinsiCandidate || '');
                const provNameNorm = normalize(getProp(provFeature.properties, PROV_NAME_KEYS));

                let filtered = ALL_REGENCY_DATA.features.filter(f => {
                    const p = f.properties || {};

                    // cek kunci-kunci penting dulu
                    for (const key of kabProvKeys) {
                        if (!Object.prototype.hasOwnProperty.call(p, key)) continue;
                        const v = normalize(p[key]);
                        if (!v) continue;

                        // 1) cocok kode: handle kasus "33.72" vs "33"
                        if (provCodeNorm) {
                            const vPrefix = v.split('.')[0]; // ambil bagian sebelum titik
                            if (v === provCodeNorm || vPrefix === provCodeNorm) return true;
                        }

                        // 2) cocok nama provinsi
                        if (provNameNorm && v === provNameNorm) return true;
                    }

                    // fallback: jika tidak ada kunci spesifik, bandingkan nama provinsi di properti WADMPR
                    const pProvName = normalize(p['WADMPR'] || p['PROVINSI'] || '');
                    if (provNameNorm && pProvName && pProvName === provNameNorm) return true;

                    return false;
                });

                // jika belum ada hasil, coba metode lemah: cari kabupaten yang menyebut nama provinsi di properti mana saja
                if ((!filtered || filtered.length === 0) && provNameNorm) {
                    filtered = ALL_REGENCY_DATA.features.filter(f => {
                        const p = f.properties || {};
                        for (const k of Object.keys(p)) {
                            if (!p[k]) continue;
                            const v = normalize(p[k]);
                            if (v && v.indexOf(provNameNorm) !== -1) return true;
                        }
                        return false;
                    });
                }

                const filteredGeoJSON = {
                    type: 'FeatureCollection',
                    features: filtered || []
                };
                loadMap(filteredGeoJSON, true);

                if (activeFeature) zoomToFeature(activeFeature);
                const backBtn = document.getElementById('back-button');
                if (backBtn) backBtn.style.display = 'inline-block';
            }

            // -------------------- reset --------------------
            function resetMap() {
                activeFeature = null;
                const backBtn = document.getElementById('back-button');
                if (backBtn) backBtn.style.display = 'none';
                loadMap(ALL_PROVINCE_DATA, false);
            }

            // -------------------- tooltip helpers --------------------
            let tooltipDiv = null;

            function createTooltip() {
                tooltipDiv = d3.select('body').append('div')
                    .attr('class', 'map-tooltip')
                    .style('position', 'absolute')
                    .style('visibility', 'hidden')
                    .style('background', 'rgba(0,0,0,0.75)')
                    .style('color', '#fff')
                    .style('padding', '6px 8px')
                    .style('border-radius', '4px')
                    .style('font-size', '13px')
                    .style('pointer-events', 'none');
            }

            function showTooltip(event, text) {
                if (!tooltipDiv) createTooltip();
                tooltipDiv.text(text).style('visibility', 'visible');
                moveTooltip(event);
            }

            function moveTooltip(event) {
                if (!tooltipDiv) return;
                const x = (event.pageX + 12) + 'px';
                const y = (event.pageY - 20) + 'px';
                tooltipDiv.style('left', x).style('top', y);
            }

            function hideTooltip() {
                if (tooltipDiv) tooltipDiv.style('visibility', 'hidden');
            }
        </script>
    @endpush


@endsection
