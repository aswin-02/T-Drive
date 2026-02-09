<?php

namespace App\Models\Admin;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends SpatiePermission
{
    use SoftDeletes;
}
