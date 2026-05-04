<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitBagian extends Model
{
    protected $table = 'unit_bagian';
    protected $fillable = ['nama','jabatan_id'];
    public $timestamps = false;

    public function jabatan(){
        return $this->hasOne(Jabatan::class, 'id','jabatan_id');
    }
}
