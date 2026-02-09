<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Share extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'owner_id',
        'shareable_type',
        'shareable_id',
        'access_type',
        'token',
        'permission',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate token for link sharing
        static::creating(function ($share) {
            if ($share->access_type === 'link' && empty($share->token)) {
                $share->token = Str::random(32);
            }
        });
    }

    /**
     * Get the owner of the share.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the shareable model (file or folder).
     */
    public function shareable()
    {
        return $this->morphTo();
    }

    /**
     * Get the share users (for email sharing).
     */
    public function shareUsers()
    {
        return $this->hasMany(ShareUser::class);
    }

    /**
     * Check if the share has expired.
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Get the share URL.
     */
    public function getShareUrlAttribute()
    {
        if ($this->access_type === 'link' && $this->token) {
            return route('shares.view', $this->token);
        }

        return null;
    }

    /**
     * Scope to get active shares (not expired).
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to get shares by token.
     */
    public function scopeByToken($query, $token)
    {
        return $query->where('token', $token);
    }
}
