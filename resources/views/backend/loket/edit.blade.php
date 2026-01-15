<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Nama Loket</label>
    <input type="text" name="nama_loket" class="form-control"
           value="{{ $loket->nama_loket }}">
</div>

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Kode Tenant</label>
    <input type="text" name="kode_tenant" class="form-control"
           value="{{ $loket->kode_tenant }}">
</div>

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Prefix</label>
    <input type="text" name="prefix_tenant" class="form-control"
           value="{{ $loket->prefix_tenant }}">
</div>

<div class="mb-7">
    <label class="required fw-semibold fs-6 mb-2">Instansi</label>
    <select name="skpd_id" class="form-control">
        @foreach ($skpd as $sk)
            <option value="{{ $sk->id }}"
                {{ $loket->skpd_id === $sk->id ? 'selected' : '' }}>
                {{ $sk->nama_skpd }}
            </option>
        @endforeach
    </select>
</div>

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Status</label>
    <select name="isaktif" class="form-select">
        <option value="1" {{ $loket->isaktif ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ !$loket->isaktif ? 'selected' : '' }}>Tidak Aktif</option>
    </select>
</div>
