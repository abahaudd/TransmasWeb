<?php

declare(strict_types=1);

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    /**
     * Templates a page can render with. Each key maps to a Blade view under
     * resources/views/cms/templates/{key}.blade.php, so pages can carry
     * entirely different layouts and styling.
     */
    public const TEMPLATES = [
        'default' => 'Default (dark / gold)',
    ];

    protected $fillable = [
        'title',
        'slug',
        'template',
        'meta_title',
        'meta_description',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('position');
    }

    public function activeBlocks(): HasMany
    {
        return $this->blocks()->where('is_active', true);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }
}
