<input type="hidden" name="hidden_id" value="{{ $user->id }}">

<!-- Nama SKPD -->
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Nama SKPD</label>
    <input type="text" name="nama_skpd"
        class="form-control form-control-solid"
        value="{{ $user->nama_skpd }}">
    <span class="text-danger error-text nama_skpd_error_edit"></span>
</div>

<!-- Kepala SKPD -->
<div class="fv-row mb-7">
    <label class="fw-semibold fs-6 mb-2">Kepala SKPD</label>
    <input type="text" name="kepala_skpd"
        class="form-control form-control-solid"
        value="{{ $user->kepala_skpd }}">
</div>

<!-- NIP Kepala -->
<div class="fv-row mb-7">
    <label class="fw-semibold fs-6 mb-2">NIP Kepala</label>
    <input type="text" name="nip_kepala"
        class="form-control form-control-solid"
        value="{{ $user->nip_kepala }}">
</div>

<!-- Status -->
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Status</label>
    <select name="isaktif" class="form-select form-select-solid">
        <option value="1" {{ $user->isaktif ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ !$user->isaktif ? 'selected' : '' }}>Tidak Aktif</option>
    </select>
</div>
