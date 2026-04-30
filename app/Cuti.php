<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $table = 'cuti';

    protected $fillable = [
        'user_id',
        'level_pengaju',
        'tahun_cuti',
        'jenis_cuti',
        'alasan_cuti',
        'lama_cuti',
        'lama_cuti_hari_kerja',
        'potong_n',
        'potong_n1',
        'potong_n2',
        'tanggal_mulai',
        'tanggal_selesai',
        'alamat',
        'no_telepon',
        'dokumen_sakit',
        'dokumen_ppk',
        'status_pengajuan',
        'status_level1',
        'status_level2',
        'approved_level1_by',
        'approved_level1_at',
        'approved_level2_by',
        'approved_level2_at',
        'rejected_reason'
    ];

    public function user()
    {
        return $this->belongsTo('App\\User', 'user_id', 'id');
    }
}
