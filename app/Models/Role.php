<?php

namespace App\Models;

use App\Traits\HasULID;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasULID;
}
