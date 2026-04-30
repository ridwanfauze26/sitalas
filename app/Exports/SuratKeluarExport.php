<?php

namespace App\Exports;

use App\SuratKeluar;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class SuratKeluarExport implements FromView,ShouldAutoSize,WithStyles
{
    protected $tahun, $bulan, $klasifikasi, $tableRecord, $suratKeluar;

    function __construct($tahun, $bulan, $klasifikasi)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;      
        $this->klasifikasi = $klasifikasi;
        $this->suratKeluar = SuratKeluar::selectRaw(
            'LEFT(nomor_surat,7) as kode,
             RIGHT(nomor_surat,19) as klasifikasi,
             isi_singkat,
             tujuan,
             DATE_FORMAT(tanggal_surat,"%d %M %Y") as tanggal,
             "1"
             ')
             ->latest()
             ->when(!empty($this->tahun), function($query){
                return $query->whereYear('tanggal_surat', $this->tahun);
             })
             ->when(!empty($this->bulan), function($query){
                return $query->whereMonth('tanggal_surat', $this->bulan);
             })
          
             ->when(!empty($this->klasifikasi), function($query){
                return $query->where('kode_surat','like', $this->klasifikasi);
             })
             ->get();
        $this->tableRecord = $this->suratKeluar->count();
    }

    public function view():View{
        $bulan = '';
        if(!empty($this->bulan)){
            $bulan=date('F', mktime(0, 0, 0, $this->bulan, 10));
        }
        $nomor=1;
        return view('exports.suratkeluar',[
            'suratkeluar' => $this->suratKeluar, 
            'nomor'=>$nomor, 
            'tahun'=>$this->tahun, 
            'bulan'=>$bulan, 
            'klasifikasi'=>$this->klasifikasi, 
            'jmlData'=>$this->tableRecord
        ]);
    }
   
    public function styles(Worksheet $sheet)
    {
        
        $sheet->getStyle('6')->getFont()->setBold(true);

        $rangeBorder = 'A6:H'.($this->tableRecord+6);

        $sheet->getStyle($rangeBorder)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
        ]);
        
    }
}
