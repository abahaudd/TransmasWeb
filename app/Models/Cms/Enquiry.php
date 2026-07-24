<?php

declare(strict_types=1);

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    public const STATUSES = [
        'new' => 'New',
        'read' => 'Read',
        'closed' => 'Closed',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'status',
    ];
}
