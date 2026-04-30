<?php

namespace App\Exports;

use App\Disposisi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DisposisiExport implements FromCollection,WithHeadings,ShouldAutoSize,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
         return Disposisi::join('surat_masuk','surat_masuk_id','=','surat_masuk.id')
             ->join('users','disposisi.user_id','=','users.id')
             ->selectRaw('surat_masuk.nomor_agenda,
                        surat_masuk.kode_surat,
                        (select nama from klasifikasi_surat where surat_masuk.kode_surat = kode),
                        surat_masuk.pengirim,
                        surat_masuk.isi_singkat,
                        users.name, 
                        DATE_FORMAT(surat_masuk.tanggal_penerimaan,"%d %M %Y"),
                        DATE_FORMAT(disposisi.updated_at,"%d %M %Y"),
                        datediff(disposisi.updated_at,surat_masuk.tanggal_penerimaan),
                        IF(datediff(disposisi.updated_at,surat_masuk.tanggal_penerimaan)<=2,0,1)')
             ->latest('disposisi.updated_at')
             ->get();
    }
    public function headings(): array
    {
        return[
            'No Agenda',
            'Kode',
            'Klasifikasi',
            'Pengirim',
            'Hal/Isi Singkat',
            'Disposisi Kepada',
            'Tanggal Surat Masuk',
            'Tanggal Disposisi',
            'Waktu Disposisi(Hari)',
            'Keterlambatan'
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // return [
        //     // Style the first row as bold text.
        //     1    => ['font' => ['bold' => true]],
        // ];
        
        $sheet->getStyle('1')->getFont()->setBold(true);
        // $sheet->cell('A1', function($cell){
        //     $cell->setBorder('thin','thin','thin','thin');
        // });
        
    }
}
