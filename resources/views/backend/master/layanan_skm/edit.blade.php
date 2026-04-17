<input type="hidden" id="hidden_id" value="{{ $data->id }}">
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">OPD (SKPD)</label>
    <select name="id_opd" id="id_opd_edit" class="form-select form-select-sm" data-control="select2" data-placeholder="Pilih OPD">
        <option></option>
        @foreach($skpds as $skpd)
            <option value="{{ $skpd->external_id_sukma }}" {{ $data->id_opd == $skpd->external_id_sukma ? 'selected' : '' }}>
                {{ $skpd->nama_skpd }}
            </option>
        @endforeach
    </select>
</div>
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">ID Layanan (Internal)</label>
    <input type="number" name="id_layanan" class="form-control mb-3 mb-lg-0" value="{{ $data->id_layanan }}" placeholder="Contoh: 123" />
</div>
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Nama Layanan</label>
    <input type="text" name="layanan" class="form-control mb-3 mb-lg-0" value="{{ $data->layanan }}" placeholder="Nama Layanan" />
</div>
