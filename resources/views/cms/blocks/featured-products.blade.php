{{--
  Featured Products Block
  Displays products tagged "Featured Deals" in the catalog as a slider
  (up to 8, via CatalogBlockComposer). Falls back to the block's static
  items when no products carry the tag yet.
--}}

@props(['data'])

@php
    $sliderItems = ($catalogItems ?? collect())->isNotEmpty()
        ? $catalogItems
        : collect($data['items'] ?? []);
    $viewAllUrl = (filled($data['link_url'] ?? null) && ($data['link_url'] ?? '#') !== '#')
        ? $data['link_url']
        : ($catalogUrl ?? null);
@endphp

<section class="cms-page py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-12">
            @if($data['kicker'] ?? null)
                <p class="cms-kicker mb-2">{{ $data['kicker'] }}</p>
            @endif

            @if($data['heading'] ?? null)
                <h2 class="cms-display cms-h2 mb-4">
                    {!! $data['heading'] !!}
                </h2>
            @endif

            @if($data['description'] ?? null)
                <p class="cms-body-text max-w-2xl mx-auto">{{ $data['description'] }}</p>
            @endif
        </div>

        {{-- Product Slider --}}
        @if($sliderItems->isNotEmpty())
            @include('cms.blocks.product-slider', ['items' => $sliderItems, 'showDescription' => true, 'showPrice' => true])
        @endif

        {{-- View All Link --}}
        @if($viewAllUrl)
            <div class="text-center mt-12">
                <a href="{{ $viewAllUrl }}" class="cms-btn cms-btn--outline">
                    {{ $data['link_label'] ?? 'View All Featured Deals' }}
                </a>
            </div>
        @endif
    </div>
</section>
