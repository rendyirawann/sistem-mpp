<div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Nama Satuan</label>
                            <input type="text" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-5"
                                 value="{{ $data->nama }}" readonly/>
                        </div>

                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Created At</label>
                            <input type="text" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-5"
                                 value="{{ $data->created_at }}" readonly/>
                        </div>

                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Updated At</label>
                            <input type="text" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-5"
                                 value="{{ $data->updated_at }}" readonly/>
                        </div>  

                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Created By</label>
                            <input type="text" class="form-control form-control-solid border border-gray-300 mb-3 mb-lg-5"
                                 value="{{ $data->user->name }}" readonly/>
                        </div>  