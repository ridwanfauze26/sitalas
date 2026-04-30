<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SOP extends Model
{
    use HasFactory;

    protected $table = 'sops';

    protected $fillable = ['judul', 'file_sop', 'tanggal']; // Sesuaikan dengan kolom tabel Anda
}
