<input type="hidden" name="hidden_id" value="{{ $user->id }}">

<div class="fv-row mb-7">


    <!--begin::Label-->
    <label for="editlogo" class="d-block fw-semibold fs-6 mb-5">Logo Skpd</label>
    <!--end::Label-->
    <!--begin::Image placeholder-->

    <!--end::Image placeholder-->
    <!--begin::Image input-->
    <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
        <!--begin::Preview existing logo-->

        <div class="symbol symbol-125px symbol-125">
            @if (empty($user->logo))
                <img id="preview-image-before-upload" src="{{ asset('assets/media/svg/files/blank-image.svg') }}"
                    alt="preview image" />
            @else
                <img id="preview-image-before-upload" src="{{ asset('storage/user/logo/' . $user->logo) }}"
                    alt="preview image" />
            @endif
        </div>

        <!--end::Preview existing logo-->
        <!--begin::Label-->
        <label for="editlogo" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change logo">
            <i class="bi bi-pencil-fill fs-7"></i>
            <!--begin::Inputs-->
            <input type="file" name="logo_skpd" id="editlogo" accept=".png, .jpg, .jpeg"
                value="{{ $user->logo }}" /> <!--end::Inputs-->
        </label>
        <!--end::Label-->
        <!--begin::Cancel-->
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel logo">
            <i class="bi bi-x fs-2"></i>
        </span>
        <!--end::Cancel-->
        <!--begin::Remove-->
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove logo">
            <i class="ki-outline ki-cross fs-2"></i>
        </span>
        <!--end::Remove-->
    </div>
    <!--end::Image input-->
    <!--begin::Hint-->
    <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
    <span class="text-danger error-text logo_skpd_error_edit"></span>
</div>
<!-- Nama SKPD -->
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Nama SKPD</label>
    <input type="text" name="nama_skpd" class="form-control form-control-solid" value="{{ $user->nama_skpd }}">
    <span class="text-danger error-text nama_skpd_error_edit"></span>
</div>

<!-- Kepala SKPD -->
<div class="fv-row mb-7">
    <label class="fw-semibold fs-6 mb-2">Kepala SKPD</label>
    <input type="text" name="kepala_skpd" class="form-control form-control-solid" value="{{ $user->kepala_skpd }}">
</div>

<!-- NIP Kepala -->
<div class="fv-row mb-7">
    <label class="fw-semibold fs-6 mb-2">NIP Kepala</label>
    <input type="text" name="nip_kepala" class="form-control form-control-solid" value="{{ $user->nip_kepala }}">
</div>

<div class="fv-row mb-7">
    <label class="fw-semibold fs-6 mb-2">ID Sukma Deli (External)</label>
    <input type="number" name="external_id_sukma" class="form-control form-control-solid"
        value="{{ $user->external_id_sukma }}" placeholder="ID dari API Sukma">
    <span class="text-danger error-text external_id_sukma_error_edit"></span>
</div>

<!-- Status -->
<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Status</label>
    <select name="isaktif" class="form-select form-select-solid">
        <option value="1" {{ $user->isaktif ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ !$user->isaktif ? 'selected' : '' }}>Tidak Aktif</option>
    </select>
</div>

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Lokasi Stand</label>
    <input type="text" name="lokasi" class="form-control form-control-solid" value="{{ $user->lokasi }}"
        placeholder="Contoh: Gedung A, Lantai 1">
    <span class="text-danger error-text lokasi_error_edit"></span>
</div>

<script type="text/javascript">
    $(document).ready(function(e) {

        $('#editlogo').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#preview-image-before-upload').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>
