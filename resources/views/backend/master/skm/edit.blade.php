<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Umur (Tahun)</label>
    <input type="number" name="umur" class="form-control mb-3 mb-lg-0" value="{{ $data->umur }}" />
</div>

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Total Nilai Survey</label>
    <input type="number" name="nilai" class="form-control mb-3 mb-lg-0" value="{{ $data->nilai }}" readonly />
    <span class="text-muted fs-8">Nilai ini adalah akumulasi dari u1 - u9.</span>
</div>

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-7 mb-2">Potensi Pungli</label>
    <select name="is_pungli" class="form-select" data-control="select2" data-hide-search="true">
        <option value="0" {{ $data->is_pungli == 0 ? 'selected' : '' }}>TIDAK (0)</option>
        <option value="1" {{ $data->is_pungli == 1 ? 'selected' : '' }}>YA (1)</option>
    </select>
</div>

<div class="fv-row mb-7 @if($data->is_pungli == 0) d-none @endif" id="container_pungli">
    <div class="mb-4">
        <label class="fw-semibold fs-7 mb-2">Kontak Pelapor Pungli</label>
        <input type="text" name="pungli_kontak" class="form-control" value="{{ $data->pungli_kontak }}" />
    </div>
    <div>
        <label class="fw-semibold fs-7 mb-2">Keterangan Pungli</label>
        <textarea name="pungli_keterangan" class="form-control" rows="3">{{ $data->pungli_keterangan }}</textarea>
    </div>
</div>

<div class="fv-row mb-7">
    <label class="fw-semibold fs-7 mb-2">Kritik & Saran</label>
    <textarea name="kritik_saran" class="form-control" rows="4">{{ $data->kritik_saran }}</textarea>
</div>

<script>
    $('[name="is_pungli"]').on('change', function() {
        if ($(this).val() == '1') {
            $('#container_pungli').removeClass('d-none');
        } else {
            $('#container_pungli').addClass('d-none');
        }
    });

    // Re-init select2 inside modal
    $('#Modal_Edit_Data .form-select').select2({
        dropdownParent: $('#Modal_Edit_Data')
    });
</script>
