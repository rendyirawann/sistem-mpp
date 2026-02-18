<?php
    
namespace App\Http\Controllers\Backend\Master\Wilayah;  

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WilayahKecamatan;
use App\Models\WilayahDesa;
use Auth;
use Jenssegers\Agent\Agent;
use DataTables; 
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\View\View;

    
class WilayahDesaController extends Controller
{


    function __construct()
    {
        $this->middleware(['auth']);
        // $this->middleware('permission:provinsi.list', ['only' => ['index','getData']]);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
  
        return view('backend.master.wilayah.desa.index');
    }

    

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $postsQuery = WilayahDesa::orderBy('id', 'asc');

            if (!empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $postsQuery->where(function ($query) use ($searchValue) {
                    $query->where('nama', 'LIKE', "%{$searchValue}%")
                          ->orWhere('id', 'LIKE', "%{$searchValue}%");
                });
            }
    
            $data = $postsQuery->select('*');
    
    
            return \DataTables::of($data) 
                
               
                
              ->addColumn('kode_pos', function ($row) {
                    if (!$row->kode_pos) return '-';

                    $items = explode(',', $row->kode_pos);
                    $badges = '';

                    foreach ($items as $item) {
                        $badges .= '<span class="badge badge-secondary me-1 mb-1">'.$item.'</span>';
                    }

                    return $badges;
                })


                ->addColumn('kode', function ($row) {
                    if (!$row->id) return '-';

                
                    $badges = '';

                        $badges .= '<span class="badge badge-secondary me-1 mb-1 ">'.$row->id.'</span>';


                    return $badges;
                })



                ->addColumn('nama', function ($row) {
                    if (!$row->nama) return '-';

                
                    $badges = '';

                        $badges .= '<span class="badge badge-secondary me-1 mb-1 ">' .$row->nama.'</span>';


                    return $badges;
                })


                ->addColumn('kode_kecamatan', function ($row) {
                    if (!$row->wilayah_kecamatan_id) return '-';

                
                    $badges = '';

                        $badges .= '<span class="badge badge-secondary me-1 mb-1 ">'.$row->wilayahkecamatan->nama.'</span>';


                    return $badges;
                })
                

                ->addColumn('created_at', function ($row) {

                    if (!$row->created_at) {
                        return '<div class="text-end"><span class="badge badge-secondary">-</span></div>';
                    }

                    return '<div class="text-end"><span class="badge badge-secondary">'.$row->created_at->format('d M Y H:i').'</span></div>';
                })

                ->addColumn('updated_at', function ($row) {

                    if (!$row->updated_at) {
                        return '<div class="text-end"> <span class="badge badge-secondary ">-</span></div>';
                    }

                    return '<div class="text-end"><span class="badge badge-secondary text-end">'.$row->updated_at->format('d M Y H:i').'</span></div>';
                })


                ->rawColumns(['kode_pos','kode','nama','created_at','updated_at','kode_kecamatan'])
                ->make(true);
        }
    }

   public function select(Request $request)
    {
        $wilayahdesa = [];
        $wilayahkecamatanID = $request->wilayahkecamatanID;
        if ($request->has('q')) {
            $search = $request->q;
            $wilayahdesa = WilayahDesa::select("id", "nama")
                ->where('wilayah_kecamatan_id', $wilayahkecamatanID)
                ->Where('nama', 'LIKE', "%$search%")
                ->get();
        } else {
            $wilayahdesa = WilayahDesa::where('wilayah_kecamatan_id', $wilayahkecamatanID)->limit(10)->get();
        }
        return response()->json($wilayahdesa);
    }
    

}
