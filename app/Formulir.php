<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Formulir extends Model
{
    protected $table = 'formulirs';
    protected $fillable = ['judul', 'file_formulir', 'tanggal'];
}
