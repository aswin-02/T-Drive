<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'folder_id',
        'original_name',
        'stored_name',
        'path',
        'size',
        'mime_type',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the file.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the folder that contains the file.
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the download URL for the file.
     */
    public function getDownloadUrlAttribute()
    {
        return route('files.download', $this->id);
    }

    /**
     * Get the file extension.
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }
    /**
     * Get all shares for this file.
     */
    public function shares()
    {
        return $this->morphMany(Share::class, 'shareable');
    }

    /**
     * Get the recent views for this file.
     */
    public function recentViews()
    {
        return $this->hasMany(RecentView::class);
    }
}
