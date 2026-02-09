<?php

namespace App\Models\Admin;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends SpatieRole
{
    use SoftDeletes;
}
