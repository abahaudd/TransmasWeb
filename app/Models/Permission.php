<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    // Extending Spatie's Permission model allows Filament to recognize it natively 
    // while keeping all under-the-hood database permissions active.
}
