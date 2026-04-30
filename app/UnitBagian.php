<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitBagian extends Model
{
    protected $table = 'unit_bagian';
    protected $fillable = ['nama'];
    public $timestamps = false;
}
