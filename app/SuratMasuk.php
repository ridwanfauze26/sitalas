<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SuratMasuk extends Model
{
    use SoftDeletes;
    protected $table="surat_masuk";
    protected $fillable = ['tanggal_penerimaan','nomor_surat','kode_surat','tanggal_surat','pengirim','isi_singkat','nomor_agenda','isi_disposisi','berkas_scan'];

    public function klasifikasi_surat(){
        return $this->belongsTo('\App\KlasifikasiSurat', 'kode', 'kode_surat');
    }
    public $timestamps = true;

    // public function getTanggalPenerimaanAttribute()
    // {
    //     return \Carbon\Carbon::parse($this->attributes['tanggal_penerimaan'])
    //      ->format('d-m-Y');
    // }

    // public function getTanggalSuratAttribute()
    // {
    //     return \Carbon\Carbon::parse($this->attributes['tanggal_surat'])
    //      ->format('d-m-Y');
    // }    
}
