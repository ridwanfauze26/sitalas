<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTelegram extends Model
{
    protected $table = 'user_telegrams';

    protected $fillable = [
        'user_id',
        'chat_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
