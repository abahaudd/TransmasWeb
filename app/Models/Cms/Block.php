<?php

declare(strict_types=1);

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    /**
     * Block types a page section can be. Each key maps to a Blade partial
     * under resources/views/cms/blocks/{key}.blade.php.
     */
    public const TYPES = [
        'hero' => 'Hero',
        'stats' => 'Stats bar',
        'steps' => 'Numbered steps',
        'feature' => 'Feature callout',
        'testimonials' => 'Testimonials',
        'featured-products' => 'Featured Products',
        'cta' => 'Call to action',
        'rich_text' => 'Rich text',
        'contact_form' => 'Contact form',
        'faq' => 'FAQ accordion',
    ];

    protected $fillable = [
        'page_id',
        'type',
        'name',
        'data',
        'position',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            // Ensure text fields in data are always strings, not arrays
            if (is_array($model->data)) {
                $sanitized = $model->data;

                // Text fields that should always be strings
                $textFields = ['body', 'heading', 'kicker', 'description', 'name'];

                foreach ($textFields as $field) {
                    if (isset($sanitized[$field]) && is_array($sanitized[$field])) {
                        $sanitized[$field] = (string) ($sanitized[$field][0] ?? '');
                    }
                }

                $model->data = $sanitized;
            }
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
