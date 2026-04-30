<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class SuratKeputusan extends Model
{
    use HasFactory;

    protected $table = 'surat_keputusan';
    
    protected $fillable = [
        'nomor_surat',
        'judul_surat',
        'tanggal_surat',
        'file'
    ];

    protected $dates = ['tanggal_surat'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'surat_keputusan_user', 'surat_keputusan_id', 'user_id')
            ->withTimestamps();
    }
} 