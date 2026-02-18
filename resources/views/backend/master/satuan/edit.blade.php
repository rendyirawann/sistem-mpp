<input type="hidden" name="hidden_id" id="hidden_id" value="{{ $data->id }}" />


<!-- Input group: Nama Brand -->
<div class="fv-row mb-7">
                            <label class="required fw-semibold fs-7 mb-2">Nama Satuan</label>
                            <input type="text" name="nama" id="Editnama" class="form-control mb-3 mb-lg-0"
                                placeholder="Contoh: Box" value="{{ $data->nama }}" />
                            <span class="text-danger error-text nama_error_edit"></span>
                        </div>

                      