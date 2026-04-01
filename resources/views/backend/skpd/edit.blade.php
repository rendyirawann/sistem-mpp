<input type="hidden" name="hidden_id" value="{{ $user->id }}">

<div class="fv-row mb-7">
    <label for="editlogo" class="d-block fw-semibold fs-6 mb-5">Logo Skpd</label>
    <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
        <div class="symbol symbol-125px symbol-125">
            @if (empty($user->logo_skpd))
                <img id="preview-image-before-upload" src="{{ asset('assets/media/svg/files/blank-image.svg') }}"
                    alt="preview image" />
            @else
                <img id="preview-image-before-upload" src="{{ asset('storage/user/logo_skpd/' . $user->logo_skpd) }}"
                    alt="preview image" />
            @endif
        </div>
        <label for="editlogo" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change logo">
            <i class="bi bi-pencil-fill fs-7"></i>
            <input type="file" name="logo_skpd" id="editlogo" accept=".png, .jpg, .jpeg" />
        </label>
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel logo">
            <i class="bi bi-x fs-2"></i>
        </span>
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove logo">
            <i class="ki-outline ki-cross fs-2"></i>
        </span>
    </div>
    <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
    <span class="text-danger error-text logo_skpd_error_edit"></span>
</div>

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Nama Instansi</label>
    <input type="text" name="nama_skpd" class="form-control form-control-solid" value="{{ $user->nama_skpd }}"
        placeholder="Dinas / Badan">
    <span class="text-danger error-text nama_skpd_error_edit"></span>
</div>

<div class="fv-row mb-7">
    <label class="fw-semibold fs-6 mb-2">Kepala Instansi</label>
    <input type="text" name="kepala_skpd" class="form-control form-control-solid" value="{{ $user->kepala_skpd }}">
</div>

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

<div class="row mb-7">
    <div class="col-md-6">
        <label class="required fw-semibold fs-6 mb-2">Status</label>
        <select name="isaktif" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
            <option value="1" {{ $user->isaktif ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ !$user->isaktif ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="required fw-semibold fs-6 mb-2">Lokasi Stand</label>
        <input type="text" name="lokasi" class="form-control form-control-solid" value="{{ $user->lokasi }}"
            placeholder="Contoh: Gedung A">
        <span class="text-danger error-text lokasi_error_edit"></span>
    </div>
</div>

<div class="separator border-primary opacity-25 my-8"></div>
<div class="mb-5">
    <h3 class="fw-bold text-primary m-0"><i class="fa fa-clock text-primary me-2"></i> Pengaturan Waktu & Antrian</h3>
    <span class="text-muted fs-7">Atur jam operasional dan kuota spesifik untuk tenant ini.</span>
</div>

<div class="row g-5 mb-7">
    <div class="col-md-4">
        <div class="border border-gray-300 rounded p-4 h-100 bg-light-primary">
            <h5 class="fw-bolder text-gray-800 mb-4 border-bottom pb-2">Senin - Kamis</h5>
            <div class="fv-row mb-4">
                <label class="required fw-semibold fs-7 mb-2">Jam Buka</label>
                <input type="text" name="buka_senin_kamis" class="form-control form-control-solid kt_time_picker"
                    value="{{ substr($user->buka_senin_kamis, 0, 5) }}" required>
            </div>
            <div class="fv-row">
                <label class="required fw-semibold fs-7 mb-2">Jam Tutup</label>
                <input type="text" name="tutup_senin_kamis" class="form-control form-control-solid kt_time_picker"
                    value="{{ substr($user->tutup_senin_kamis, 0, 5) }}" required>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="border border-gray-300 rounded p-4 h-100 bg-light-success">
            <h5 class="fw-bolder text-gray-800 mb-4 border-bottom pb-2">Jumat</h5>
            <div class="fv-row mb-4">
                <label class="required fw-semibold fs-7 mb-2">Jam Buka</label>
                <input type="text" name="buka_jumat" class="form-control form-control-solid kt_time_picker"
                    value="{{ substr($user->buka_jumat, 0, 5) }}" required>
            </div>
            <div class="fv-row">
                <label class="required fw-semibold fs-7 mb-2">Jam Tutup</label>
                <input type="text" name="tutup_jumat" class="form-control form-control-solid kt_time_picker"
                    value="{{ substr($user->tutup_jumat, 0, 5) }}" required>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="border border-gray-300 rounded p-4 h-100 bg-light-warning">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                <h5 class="fw-bolder text-gray-800 mb-0">Sabtu</h5>
                <div class="form-check form-switch form-check-custom form-check-solid form-check-success"
                    data-bs-toggle="tooltip" title="Aktifkan jika melayani di hari Sabtu">
                    <input class="form-check-input h-20px w-30px" type="checkbox" name="is_sabtu_buka"
                        value="1" id="sabtuBukaEdit" {{ $user->is_sabtu_buka ? 'checked' : '' }} />
                </div>
            </div>
            <div class="fv-row mb-4">
                <label class="required fw-semibold fs-7 mb-2">Jam Buka</label>
                <input type="text" name="buka_sabtu" class="form-control form-control-solid kt_time_picker"
                    value="{{ substr($user->buka_sabtu, 0, 5) }}" required>
            </div>
            <div class="fv-row">
                <label class="required fw-semibold fs-7 mb-2">Jam Tutup</label>
                <input type="text" name="tutup_sabtu" class="form-control form-control-solid kt_time_picker"
                    value="{{ substr($user->tutup_sabtu, 0, 5) }}" required>
            </div>
        </div>
    </div>
</div>

<div class="fv-row mb-7">
    <label class="required fw-semibold fs-6 mb-2">Batas Kuota Antrian Harian</label>
    <div class="position-relative">
        <input type="number" name="kuota_harian" class="form-control form-control-solid ps-12"
            value="{{ $user->kuota_harian }}" required>
        <div class="position-absolute top-0 start-0 h-100 d-flex align-items-center ps-4">
            <i class="fa fa-users text-gray-500 fs-4"></i>
        </div>
    </div>
    <div class="form-text">Ketik angka '0' jika antrian tidak ingin dibatasi.</div>
</div>

<div class="border border-danger border-dashed rounded bg-light-danger mb-5 p-4">
    <div class="form-check form-switch form-check-custom form-check-solid form-check-danger d-flex align-items-center">
        <input class="form-check-input h-30px w-50px me-3" type="checkbox" name="is_force_close" value="1"
            id="forceCloseEdit" {{ $user->is_force_close ? 'checked' : '' }} />
        <label class="form-check-label text-danger fw-bolder fs-6" for="forceCloseEdit">
            TUTUP PAKSA LAYANAN INI SEKARANG (Force Close)
        </label>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Preview Image Script
        $('#editlogo').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#preview-image-before-upload').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Re-initialize Select2 inside AJAX modal
        $('[data-control="select2"]').select2({
            dropdownParent: $('#Modal_Edit_Data'),
            minimumResultsForSearch: Infinity // Sembunyikan search box untuk status
        });

        // ========================================================
        // INISIALISASI FLATPICKR (TIME PICKER METRONIC)
        // ========================================================
        $(".kt_time_picker").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            // PENTING: appendTo ini memastikan popup jam tidak tersembunyi/error di belakang modal
            appendTo: document.getElementById('Modal_Edit_Data'),
            disableMobile: true // Memastikan UI Metronic tetap tampil cantik di layar HP, tidak tertimpa UI jam bawaan HP
        });
    });
</script>
