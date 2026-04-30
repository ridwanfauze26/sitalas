<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CutiTahunanBalance extends Model
{
    protected $table = 'cuti_tahunan_balances';

    protected $fillable = [
        'user_id',
        'tahun',
        'jatah',
        'dipakai',
    ];

    public function user()
    {
        return $this->belongsTo('App\\User', 'user_id', 'id');
    }
}
