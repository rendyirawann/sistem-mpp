@extends('backend.layout.app')
@section('title', $form ? 'Edit Form Persyaratan' : 'Tambah Form Persyaratan')

@push('stylesheets')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        .fb-section { border: 1px solid var(--bs-gray-300); border-radius: 12px; padding: 16px; margin-bottom: 16px; background: var(--bs-gray-100); }
        .fb-field { border: 1px solid var(--bs-gray-300); border-radius: 10px; padding: 12px; margin-bottom: 10px; background: #fff; }
        .fb-field .row > div { margin-bottom: 8px; }
        .fb-toolbar { display: flex; gap: 6px; }
        .fb-loket-group { border-bottom: 1px solid var(--bs-gray-200); padding: 8px 0; }
        .fb-loket-group h6 { font-size: 12px; text-transform: uppercase; color: var(--bs-gray-600); margin-bottom: 6px; }
        .fb-loket-chk { display: inline-flex; align-items: center; gap: 6px; margin: 3px 12px 3px 0; font-size: 13px; }
        label.form-label { font-size: 12px; font-weight: 600; }
    </style>
@endpush

@section('content')
    <div id="kt_app_toolbar" class="app-toolbar py-4">
        <div class="app-container container-fluid">
            <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">{{ $form ? 'Edit' : 'Tambah' }} Form Persyaratan</h1>
            <ul class="breadcrumb fw-semibold fs-7 my-1">
                <li class="breadcrumb-item text-muted"><a href="{{ route('form-persyaratan.index') }}">Form Persyaratan</a></li>
                <li class="breadcrumb-item text-gray-900">{{ $form ? 'Edit' : 'Tambah' }}</li>
            </ul>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-fluid">
            <div class="row g-5">
                {{-- KIRI: Info + Builder --}}
                <div class="col-lg-8">
                    <div class="card border border-gray-300 mb-5">
                        <div class="card-header"><h3 class="card-title fw-bold">Informasi Form</h3></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label class="form-label required">Kode</label>
                                    <input type="text" id="f-kode" class="form-control form-control-sm" value="{{ $form->kode ?? '' }}" placeholder="mis. F-1.02">
                                </div>
                                <div class="col-md-8 mb-4">
                                    <label class="form-label required">Nama Form</label>
                                    <input type="text" id="f-nama" class="form-control form-control-sm" value="{{ $form->nama ?? '' }}" placeholder="Nama form">
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Deskripsi</label>
                                    <input type="text" id="f-deskripsi" class="form-control form-control-sm" value="{{ $form->deskripsi ?? '' }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" id="f-aktif" {{ !$form || $form->is_active ? 'checked' : '' }}>
                                        <span class="form-check-label fw-semibold">Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border border-gray-300">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title fw-bold">Builder Field</h3>
                            <div class="card-toolbar"><button class="btn btn-sm btn-light-primary" id="btnAddSection"><i class="ki-outline ki-plus fs-4"></i> Tambah Section</button></div>
                        </div>
                        <div class="card-body">
                            <div id="builder"></div>
                        </div>
                    </div>
                </div>

                {{-- KANAN: Taut ke layanan --}}
                <div class="col-lg-4">
                    <div class="card border border-gray-300">
                        <div class="card-header"><h3 class="card-title fw-bold">Tautkan ke Layanan</h3></div>
                        <div class="card-body">
                            <input type="text" id="loketSearch" class="form-control form-control-sm mb-3" placeholder="Cari layanan / instansi...">
                            <div style="max-height:520px;overflow:auto;">
                                @foreach ($skpdList as $skpd)
                                    @if ($skpd->lokets->count())
                                        <div class="fb-loket-group" data-skpd="{{ \Illuminate\Support\Str::lower($skpd->nama_skpd) }}">
                                            <h6>{{ $skpd->nama_skpd }}</h6>
                                            @foreach ($skpd->lokets as $lk)
                                                <label class="fb-loket-chk" data-nama="{{ \Illuminate\Support\Str::lower($lk->nama_loket) }}">
                                                    <input type="checkbox" class="form-check-input loket-chk" value="{{ $lk->id }}"
                                                        {{ in_array($lk->id, $attached) ? 'checked' : '' }}>
                                                    <span>{{ $lk->nama_loket }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-5">
                <a href="{{ route('form-persyaratan.index') }}" class="btn btn-light">Batal</a>
                <button class="btn btn-primary" id="btnSave"><i class="ki-outline ki-check fs-3"></i> Simpan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const IS_EDIT = @json((bool) $form);
            const SAVE_URL = @json($form ? route('form-persyaratan.update', $form->id) : route('form-persyaratan.store'));
            const EXISTING = @json($form?->skema ?? null);
            const TYPES = ['text', 'number', 'date', 'time', 'textarea', 'select', 'radio', 'checkbox', 'file', 'repeater', 'wilayah'];

            // ---- state ----
            let state = (EXISTING && Array.isArray(EXISTING.sections) && EXISTING.sections.length)
                ? JSON.parse(JSON.stringify(EXISTING))
                : { sections: [{ title: 'Section 1', note: '', fields: [] }] };
            state.sections.forEach(s => (s.fields = s.fields || []));

            function esc(s) { return String(s == null ? '' : s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c])); }
            function slug(s) { return String(s || '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, ''); }
            function optTxt(a) { return Array.isArray(a) ? a.map(o => typeof o === 'object' ? o.value : o).join(', ') : ''; }
            function txtOpt(t) { return String(t || '').split(',').map(x => x.trim()).filter(Boolean); }
            function showIfTxt(s) { return (s && s.field) ? (s.field + ': ' + (s.in || []).join(' | ')) : ''; }
            function txtShowIf(t) { t = String(t || '').trim(); if (!t) return null; const i = t.indexOf(':'); if (i < 0) return null; const f = t.slice(0, i).trim(); const vals = t.slice(i + 1).split('|').map(x => x.trim()).filter(Boolean); return f ? { field: f, in: vals } : null; }
            function colTxt(cols) { return (cols || []).map(c => [c.key, c.label, c.type || 'text', (c.options || []).join(';')].join('|')).join('\n'); }
            function txtCol(t) {
                return String(t || '').split('\n').map(l => l.trim()).filter(Boolean).map(function (l) {
                    const p = l.split('|'); const c = { key: slug(p[0] || p[1] || ''), label: (p[1] || p[0] || '').trim(), type: (p[2] || 'text').trim() };
                    if (p[3]) c.options = p[3].split(';').map(x => x.trim()).filter(Boolean);
                    return c;
                }).filter(c => c.key);
            }

            // ---- render ----
            const host = document.getElementById('builder');
            function render() {
                host.innerHTML = state.sections.map(sectionHtml).join('') || '<div class="text-muted">Belum ada section.</div>';
            }
            function sectionHtml(sec, si) {
                const fields = (sec.fields || []).map((f, fi) => fieldHtml(f, si, fi)).join('');
                return `<div class="fb-section" data-si="${si}">
                    <div class="d-flex gap-2 mb-2">
                        <input class="form-control form-control-sm fw-bold" data-si="${si}" data-prop="title" value="${esc(sec.title)}" placeholder="Judul Section">
                        <div class="fb-toolbar">
                            <button class="btn btn-icon btn-sm btn-light" data-act="sec-up" data-si="${si}" title="Naik">↑</button>
                            <button class="btn btn-icon btn-sm btn-light" data-act="sec-down" data-si="${si}" title="Turun">↓</button>
                            <button class="btn btn-icon btn-sm btn-light-danger" data-act="sec-del" data-si="${si}" title="Hapus section">&times;</button>
                        </div>
                    </div>
                    <input class="form-control form-control-sm mb-3" data-si="${si}" data-prop="note" value="${esc(sec.note || '')}" placeholder="Catatan section (opsional)">
                    <div>${fields}</div>
                    <button class="btn btn-sm btn-light-primary mt-1" data-act="field-add" data-si="${si}"><i class="ki-outline ki-plus fs-5"></i> Tambah Field</button>
                </div>`;
            }
            function fieldHtml(f, si, fi) {
                const isOpt = ['select', 'radio', 'checkbox'].indexOf(f.type) >= 0;
                const isRep = f.type === 'repeater';
                const typeOpts = TYPES.map(t => `<option value="${t}" ${f.type === t ? 'selected' : ''}>${t}</option>`).join('');
                return `<div class="fb-field" data-si="${si}" data-fi="${fi}">
                    <div class="row">
                        <div class="col-md-5"><label class="form-label">Label</label>
                            <input class="form-control form-control-sm" data-si="${si}" data-fi="${fi}" data-prop="label" value="${esc(f.label)}"></div>
                        <div class="col-md-4"><label class="form-label">Key</label>
                            <input class="form-control form-control-sm" data-si="${si}" data-fi="${fi}" data-prop="key" value="${esc(f.key)}" placeholder="otomatis dari label"></div>
                        <div class="col-md-3"><label class="form-label">Tipe</label>
                            <select class="form-select form-select-sm" data-si="${si}" data-fi="${fi}" data-prop="type">${typeOpts}</select></div>
                        <div class="col-md-4"><label class="form-check form-check-sm mt-2">
                            <input class="form-check-input" type="checkbox" data-si="${si}" data-fi="${fi}" data-prop="required" ${f.required ? 'checked' : ''}>
                            <span class="form-check-label fs-8 ms-1">Wajib</span></label></div>
                        ${isOpt ? `<div class="col-md-8"><label class="form-label">Opsi (pisah koma)</label>
                            <input class="form-control form-control-sm" data-si="${si}" data-fi="${fi}" data-prop="options" value="${esc(optTxt(f.options))}"></div>` : ''}
                        ${isRep ? `<div class="col-md-12"><label class="form-label">Kolom repeater (satu per baris: key|label|type|opsi;opsi)</label>
                            <textarea class="form-control form-control-sm" rows="3" data-si="${si}" data-fi="${fi}" data-prop="columns">${esc(colTxt(f.columns))}</textarea></div>` : ''}
                        <div class="col-md-12"><label class="form-label">Tampil jika (opsional: keyLain: nilai1 | nilai2)</label>
                            <input class="form-control form-control-sm" data-si="${si}" data-fi="${fi}" data-prop="showif" value="${esc(showIfTxt(f.showIf))}"></div>
                    </div>
                    <div class="fb-toolbar mt-2 justify-content-end">
                        <button class="btn btn-icon btn-sm btn-light" data-act="field-up" data-si="${si}" data-fi="${fi}">↑</button>
                        <button class="btn btn-icon btn-sm btn-light" data-act="field-down" data-si="${si}" data-fi="${fi}">↓</button>
                        <button class="btn btn-icon btn-sm btn-light-danger" data-act="field-del" data-si="${si}" data-fi="${fi}">&times;</button>
                    </div>
                </div>`;
            }

            // ---- edit (delegated, no re-render for text) ----
            host.addEventListener('input', onEdit);
            host.addEventListener('change', function (e) { onEdit(e); if (e.target.dataset.prop === 'type') render(); });
            function onEdit(e) {
                const t = e.target; const si = t.dataset.si, fi = t.dataset.fi, prop = t.dataset.prop;
                if (si == null || prop == null) return;
                if (fi == null) { // section prop
                    state.sections[si][prop] = t.value; return;
                }
                const f = state.sections[si].fields[fi];
                if (prop === 'required') f.required = t.checked;
                else if (prop === 'options') f.options = txtOpt(t.value);
                else if (prop === 'columns') f.columns = txtCol(t.value);
                else if (prop === 'showif') { const s = txtShowIf(t.value); if (s) f.showIf = s; else delete f.showIf; }
                else f[prop] = t.value;
            }

            // ---- structural (delegated click) ----
            host.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-act]'); if (!btn) return;
                e.preventDefault();
                const si = +btn.dataset.si, fi = btn.dataset.fi != null ? +btn.dataset.fi : null, act = btn.dataset.act;
                const secs = state.sections;
                if (act === 'sec-del') { if (confirm('Hapus section ini beserta field-nya?')) secs.splice(si, 1); }
                else if (act === 'sec-up' && si > 0) { [secs[si - 1], secs[si]] = [secs[si], secs[si - 1]]; }
                else if (act === 'sec-down' && si < secs.length - 1) { [secs[si + 1], secs[si]] = [secs[si], secs[si + 1]]; }
                else if (act === 'field-add') { secs[si].fields.push({ label: '', key: '', type: 'text', required: false }); }
                else if (act === 'field-del') { secs[si].fields.splice(fi, 1); }
                else if (act === 'field-up' && fi > 0) { const a = secs[si].fields; [a[fi - 1], a[fi]] = [a[fi], a[fi - 1]]; }
                else if (act === 'field-down' && fi < secs[si].fields.length - 1) { const a = secs[si].fields; [a[fi + 1], a[fi]] = [a[fi], a[fi + 1]]; }
                render();
            });

            document.getElementById('btnAddSection').addEventListener('click', function (e) {
                e.preventDefault();
                state.sections.push({ title: 'Section ' + (state.sections.length + 1), note: '', fields: [] });
                render();
            });

            // ---- loket search ----
            document.getElementById('loketSearch').addEventListener('input', function () {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.fb-loket-group').forEach(function (g) {
                    const skpdMatch = (g.dataset.skpd || '').indexOf(q) >= 0;
                    let anyVisible = false;
                    g.querySelectorAll('.fb-loket-chk').forEach(function (c) {
                        const show = skpdMatch || (c.dataset.nama || '').indexOf(q) >= 0;
                        c.style.display = show ? '' : 'none'; if (show) anyVisible = true;
                    });
                    g.style.display = anyVisible ? '' : 'none';
                });
            });

            // ---- build & save ----
            function buildSkema() {
                const sections = state.sections.map(function (sec) {
                    const fields = (sec.fields || []).filter(f => (f.label || f.key)).map(function (f) {
                        const key = slug(f.key) || slug(f.label);
                        const out = { key, label: f.label || key, type: f.type || 'text' };
                        if (f.required) out.required = true;
                        if (['select', 'radio', 'checkbox'].indexOf(f.type) >= 0 && (f.options || []).length) out.options = f.options;
                        if (f.type === 'repeater' && (f.columns || []).length) out.columns = f.columns;
                        if (f.showIf && f.showIf.field) out.showIf = f.showIf;
                        return out;
                    });
                    const s = { title: sec.title || '', fields };
                    if (sec.note) s.note = sec.note;
                    return s;
                }).filter(s => s.fields.length);
                return { sections };
            }

            document.getElementById('btnSave').addEventListener('click', function () {
                const kode = document.getElementById('f-kode').value.trim();
                const nama = document.getElementById('f-nama').value.trim();
                if (!kode || !nama) { Swal.fire('Lengkapi', 'Kode & Nama form wajib diisi.', 'warning'); return; }
                const skema = buildSkema();
                if (!skema.sections.length) { Swal.fire('Kosong', 'Minimal 1 section dengan 1 field.', 'warning'); return; }

                const fd = new FormData();
                fd.append('_token', CSRF);
                if (IS_EDIT) fd.append('_method', 'PUT');
                fd.append('kode', kode);
                fd.append('nama', nama);
                fd.append('deskripsi', document.getElementById('f-deskripsi').value);
                fd.append('is_active', document.getElementById('f-aktif').checked ? '1' : '0');
                fd.append('skema', JSON.stringify(skema));
                document.querySelectorAll('.loket-chk:checked').forEach(c => fd.append('loket_ids[]', c.value));

                const btn = this; btn.disabled = true;
                fetch(SAVE_URL, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: fd })
                    .then(async r => ({ ok: r.ok, j: await r.json().catch(() => ({})) }))
                    .then(({ ok, j }) => {
                        btn.disabled = false;
                        if (ok && j.success) { Swal.fire('Berhasil', j.message, 'success').then(() => location.href = j.redirect); }
                        else Swal.fire('Gagal', j.message || (j.errors ? Object.values(j.errors)[0][0] : 'Terjadi kesalahan'), 'error');
                    })
                    .catch(() => { btn.disabled = false; Swal.fire('Error', 'Gagal menyimpan.', 'error'); });
            });

            render();
        })();
    </script>
@endpush
