<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $total;
    protected $labelPeriode;

    public function __construct($data, $total, $labelPeriode)
    {
        $this->data = $data;
        $this->total = $total;
        $this->labelPeriode = $labelPeriode;
    }

    public function view(): View
    {
        // 🔥 UBAH KE FILE VIEW KHUSUS EXCEL
        return view('backend.dashboard.export_excel', [
            'data' => $this->data,
            'total' => $this->total,
            'labelPeriode' => $this->labelPeriode
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
