<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareUser extends Model
{
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'share_id',
        'email',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set created_at
        static::creating(function ($shareUser) {
            if (empty($shareUser->created_at)) {
                $shareUser->created_at = now();
            }
        });
    }

    /**
     * Get the share that this user belongs to.
     */
    public function share()
    {
        return $this->belongsTo(Share::class);
    }
}
