<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $fillable = [
        'refer_id',
        'model',
        'action',
        'old_data',
        'new_data',
        'ip',
        'user_id',
        'log_time',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'log_time' => 'datetime',
    ];
}
