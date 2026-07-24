<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // Extending Spatie's Role model allows Filament to recognize it natively 
    // while keeping all under-the-hood database permissions active.
}
