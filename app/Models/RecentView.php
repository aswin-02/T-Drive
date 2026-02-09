<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentView extends Model
{
    use HasFactory;

    // Explicitly set the table name if it's not the default
    protected $table = 'recent_views';

    protected $fillable = [
        'user_id',
        'file_id',
        'viewed_at',
    ];

    public $timestamps = false; // We use 'viewed_at' explicitly

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
