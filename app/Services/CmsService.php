<?php

namespace App\Services;

use App\Models\Cms\Page;

/**
 * Front-facing CMS: resolves published pages with their active blocks.
 * Modules can use this service to render CMS-managed content anywhere.
 */
class CmsService
{
    /**
     * Legacy slugs kept for backward compatibility after content renames.
     * Old links continue to work while new canonical slugs are in use.
     *
     * @var array<string, string>
     */
    private const LEGACY_SLUG_ALIASES = [
        'trade-faq' => 'faq',
        'request-access' => 'service-access',
        'shipping-policy' => 'delivery-policy',
        'return-policy' => 'service-policy',
    ];

    public function page(string $slug): ?Page
    {
        $resolvedSlug = self::LEGACY_SLUG_ALIASES[$slug] ?? $slug;

        return Page::query()
            ->published()
            ->where('slug', $resolvedSlug)
            ->with('activeBlocks')
            ->first();
    }

    /**
     * The Blade view a page renders with, falling back to the default template.
     */
    public function templateView(Page $page): string
    {
        
        $view = "cms.templates.{$page->template}";

        return view()->exists($view) ? $view : 'cms.templates.default';
    }
}
