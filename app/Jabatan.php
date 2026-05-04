<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $fillable = ['nama', 'level'];
    public $timestamps = false;

    public function unitBagian(){
        return $this->belongsTo(UnitBagian::class);
    }
}
