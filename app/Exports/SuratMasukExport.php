<?php

namespace App\Exports;

use App\SuratMasuk;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class SuratMasukExport implements FromView,ShouldAutoSize,WithStyles
{
    protected $tahun, $bulan, $klasifikasi, $tableRecord, $suratMasuk;

    function __construct($tahun, $bulan, $klasifikasi)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->klasifikasi = $klasifikasi;
        $this->suratMasuk = SuratMasuk::selectRaw(
            '"",
             nomor_agenda,
             nomor_surat,
             kode_surat,
             pengirim,
             isi_singkat,
             DATE_FORMAT(tanggal_surat,"%d-%m-%Y") as tanggal,
             DATE_FORMAT(tanggal_penerimaan,"%d-%m-%Y") as tanggal_terima,
             "1"')
             ->latest()
             ->when(!empty($this->tahun), function($query){
                return $query->whereYear('tanggal_penerimaan', $this->tahun);
            })
             ->when(!empty($this->bulan), function($query){
                return $query->whereMonth('tanggal_penerimaan', $this->bulan);
             })
             ->when(!empty($this->klasifikasi), function($query){
                return $query->where('kode_surat','like', $this->klasifikasi);
             })
             ->get();
        $this->tableRecord = $this->suratMasuk->count();
    }

    public function view():View{
        $bulan='';
        if(!empty($this->bulan)){
            $bulan=date('F', mktime(0, 0, 0, $this->bulan, 10));
        }
        $nomor=1;
        return view('exports.suratmasuk',[
            'suratmasuk' => $this->suratMasuk, 
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

        $rangeBorder = 'A6:J'.($this->tableRecord+6);

        $sheet->getStyle($rangeBorder)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
        ]);
        
    }
}

