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
    protected $type;

    // Terima data dari Controller melalui Constructor
    public function __construct($data, $total, $labelPeriode)
    {
        $this->data = $data;
        $this->total = $total;
        $this->labelPeriode = $labelPeriode;
        $this->type = 'excel'; // Force type
    }

    public function view(): View
    {
        // Kita gunakan View yang sama dengan PDF tadi
        return view('backend.dashboard.export', [
            'data' => $this->data,
            'total' => $this->total,
            'labelPeriode' => $this->labelPeriode,
            'type' => $this->type
        ]);
    }

    // Optional: Tambah border otomatis biar rapi di Excel
    public function styles(Worksheet $sheet)
    {
        return [
            // Style seluruh tabel
            1    => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
