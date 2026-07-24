{{-- Default CMS page template: shared chrome from cms.layout, body composed of the page's active blocks. --}}
@php
    $toText = static function ($value, string $fallback = ''): string {
        if (is_string($value) || is_numeric($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if (is_string($item) || is_numeric($item)) {
                    return (string) $item;
                }
            }
        }

        return $fallback;
    };

    $metaTitle = $toText($page->meta_title ?? null, $toText($page->title ?? null, ''));
    $metaDescription = $toText($page->meta_description ?? null, '');
@endphp
@extends('cms.layout')

@section('title', $metaTitle)

@if($metaDescription !== '')
    @section('meta_description', $metaDescription)
@endif

@section('content')
    <main class="cms-content-remove">
        @forelse($page->activeBlocks as $block)
            <div class="cms-content-block">
                @includeIf('cms.blocks.' . $block->type, ['data' => $block->data ?? []])
            </div>
        @empty
            <section class="cms-content-empty max-w-7xl mx-auto px-8 lg:px-16 py-16">
                <div class="cms-panel p-8">
                    <p class="cms-muted text-sm">No content is available for this page yet.</p>
                </div>
            </section>
        @endforelse
    </main>
@endsection
