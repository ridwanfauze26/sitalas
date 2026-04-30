<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'jabatan_id', 'unit_bagian_id', 'unit_bagian_nama', 'nip', 'role', 'telegram_link_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function jabatan()
    {
        return $this->hasOne('App\Jabatan', 'id', 'jabatan_id');
    }

    public function unitBagian()
    {
        return $this->hasOne('App\UnitBagian', 'id', 'unit_bagian_id');
    }

    public function telegrams()
    {
        return $this->hasMany(UserTelegram::class, 'user_id');
    }

    public function activeTelegram()
    {
        return $this->hasOne(UserTelegram::class, 'user_id')->where('is_active', true);
    }

    public function getActiveTelegramChatIdAttribute()
    {
        $tg = $this->relationLoaded('activeTelegram') ? $this->activeTelegram : $this->activeTelegram()->first();
        return $tg ? (string) $tg->chat_id : null;
    }

    public function getCutiLevelAttribute()
    {
        $jabatanLevel = $this->jabatan ? $this->jabatan->level : null;
        if ($jabatanLevel !== null && $jabatanLevel !== '') {
            return (int) $jabatanLevel;
        }

        $jabatanNama = $this->jabatan ? (string) $this->jabatan->nama : '';
        $jabatanNama = trim(mb_strtolower($jabatanNama));

        if ($jabatanNama === trim(mb_strtolower('Kepala Balai'))) {
            return 1;
        }

        $level2 = [
            trim(mb_strtolower('Sub Koordinator Substansi Pelayanan Teknik')),
            trim(mb_strtolower('Kepala Sub Bagian Tata Usaha')),
            trim(mb_strtolower('Sub Koordinator Substansi Penyiapan Sampel')),
        ];

        if (in_array($jabatanNama, $level2, true)) {
            return 2;
        }

        return 3;
    }

    public function isCutiApproverLevel1()
    {
        return (int) $this->cuti_level === 1;
    }

    public function isCutiApproverLevel2()
    {
        return (int) $this->cuti_level === 2;
    }

    public function isCutiApprover()
    {
        return $this->role === 'admin' || $this->isCutiApproverLevel1() || $this->isCutiApproverLevel2();
    }
}
