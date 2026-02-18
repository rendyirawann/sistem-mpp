<input type="hidden" name="hidden_id" id="hidden_id" value="{{ $role->id }}" />
<!--begin::Input group-->
<div class="fv-row mb-7">
    <!--begin::Label-->
    <label class="required fs-6 fw-semibold mb-2">Role Name</label>
    <!--end::Label-->
    <!--begin::Input-->
    <input type="text" name="name" id="name" class="form-control form-control-solid mb-3 mb-lg-0"
        placeholder="Role Name" value="{{ $role->name }}" />
    <span class="text-danger error-text name_error_edit"></span>
    <!--end::Input-->
</div>
<!--end::Input group-->
<!--begin::Input group-->
<div class="fv-row mb-7">
    <!--begin::Label-->
    <label class="required fs-6 fw-semibold mb-2">Permissions</label>
    <span class="text-danger error-text permission_error_edit"></span>
    <!--end::Label-->
    <!--begin::Input-->
    <!--begin::Input group-->
    <div class="row g-9 mb-8">
        <!--begin::Col-->
        @foreach ($permission as $category => $categoryItems)
            <div class="col-md-4 fv-row">
                <label class="fs-6 fw-semibold mb-2">{{ $category }}</label>
                <!--begin::Wrapper-->
                <div>
                    @foreach ($categoryItems as $item)
                        <!--begin::Checkbox-->
                        <label class="form-check form-check-sm form-check-custom mb-2 me-5 me-lg-2">
                            <input class="form-check-input" type="checkbox" name="permission[]" id="{{ $item->id }}"
                                value="{{ $item->id }}"
                                {{ in_array($item->id, $rolePermissions) ? 'checked' : '' }} />
                            <span class="form-check-label">{{ $item->name }}</span>
                        </label>
                        <!--end::Checkbox-->
                    @endforeach
                </div>
                <!--end::Wrapper-->
            </div>
        @endforeach
        <!--end::Col-->
    </div>
    <!--end::Input group-->
    <!--end::Input-->
</div>
<!--end::Input group-->
