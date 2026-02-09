<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'path',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the folder.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the files in this folder.
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Get the parent folder.
     */
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Get the child folders.
     */
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }
}
