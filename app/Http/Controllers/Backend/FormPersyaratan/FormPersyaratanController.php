<?php

namespace App\Http\Controllers\Backend\FormPersyaratan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FormPersyaratan;
use App\Models\FormPersyaratanValue;
use App\Models\Skpd;
use Yajra\DataTables\Facades\DataTables;

class FormPersyaratanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('Superadmin')) abort(403, 'Khusus Superadmin.');
            return $next($request);
        });
    }

    public function index()
    {
        return view('backend.form_persyaratan.index');
    }

    public function data()
    {
        $q = FormPersyaratan::query()->withCount('lokets');

        return DataTables::of($q)
            ->addColumn('field_count', fn ($f) => count($f->fields()))
            ->addColumn('status', fn ($f) => $f->is_active
                ? '<span class="badge badge-light-success">Aktif</span>'
                : '<span class="badge badge-light-danger">Nonaktif</span>')
            ->addColumn('taut', fn ($f) => '<span class="badge badge-light-primary">' . $f->lokets_count . ' layanan</span>')
            ->addColumn('action', function ($f) {
                return '<div class="text-end">'
                    . '<a href="' . route('form-persyaratan.edit', $f->id) . '" class="btn btn-sm btn-light-warning me-1"><i class="ki-outline ki-pencil fs-5"></i> Edit</a>'
                    . '<button class="btn btn-sm btn-light-danger btn-del" data-id="' . $f->id . '" data-nama="' . e($f->nama) . '"><i class="ki-outline ki-trash fs-5"></i></button>'
                    . '</div>';
            })
            ->rawColumns(['status', 'taut', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('backend.form_persyaratan.form', [
            'form'     => null,
            'skpdList' => $this->skpdList(),
            'attached' => [],
        ]);
    }

    public function edit($id)
    {
        $form = FormPersyaratan::findOrFail($id);
        return view('backend.form_persyaratan.form', [
            'form'     => $form,
            'skpdList' => $this->skpdList(),
            'attached' => $form->lokets()->pluck('lokets.id')->all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $form = FormPersyaratan::create($data);
        $form->lokets()->sync($request->input('loket_ids', []));

        return response()->json(['success' => true, 'message' => 'Form persyaratan dibuat.', 'redirect' => route('form-persyaratan.index')]);
    }

    public function update(Request $request, $id)
    {
        $form = FormPersyaratan::findOrFail($id);
        $form->update($this->validated($request));
        $form->lokets()->sync($request->input('loket_ids', []));

        return response()->json(['success' => true, 'message' => 'Form persyaratan diperbarui.', 'redirect' => route('form-persyaratan.index')]);
    }

    public function destroy($id)
    {
        $form = FormPersyaratan::findOrFail($id);

        // Lindungi jika sudah dipakai (ada isian warga)
        if (FormPersyaratanValue::where('form_persyaratan_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Form tidak dapat dihapus karena sudah ada isian warga. Nonaktifkan saja.',
            ], 422);
        }

        $form->lokets()->detach();
        $form->delete();

        return response()->json(['success' => true, 'message' => 'Form persyaratan dihapus.']);
    }

    // ===== helpers =====
    private function validated(Request $request): array
    {
        $request->validate([
            'kode'  => 'required|string|max:40',
            'nama'  => 'required|string|max:255',
            'skema' => 'required|string',
        ], [
            'kode.required'  => 'Kode form wajib diisi',
            'nama.required'  => 'Nama form wajib diisi',
            'skema.required' => 'Skema field wajib diisi (minimal 1 section & field)',
        ]);

        $skema = json_decode($request->input('skema'), true);
        if (!is_array($skema) || empty($skema['sections'])) {
            abort(response()->json(['success' => false, 'message' => 'Skema field tidak valid / kosong.'], 422));
        }

        return [
            'kode'      => trim($request->kode),
            'nama'      => trim($request->nama),
            'deskripsi' => $request->deskripsi,
            'skema'     => $skema,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function skpdList()
    {
        return Skpd::where('isaktif', 1)
            ->with(['lokets' => fn ($q) => $q->where('isaktif', 1)->orderBy('nama_loket')])
            ->orderByRaw("CASE WHEN nama_skpd LIKE '%Catatan Sipil%' OR nama_skpd LIKE '%Pencatatan Sipil%' OR nama_skpd LIKE '%Kependudukan%' THEN 0 ELSE 1 END")
            ->orderBy('nama_skpd')
            ->get();
    }
}
