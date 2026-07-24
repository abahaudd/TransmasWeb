{{--
  Product Slider
  Horizontal scroll-snap carousel for up to 8 product cards.
  Shows 4 cards per view on desktop, 2 on tablet, 1 on mobile.
  Usage: @include('cms.blocks.product-slider', ['items' => $items, 'showDescription' => true])
  Prices are hidden unless showPrice is passed as true.
--}}

@props(['items' => [], 'showDescription' => false, 'showPrice' => false])

@once
    <style>
        .cms-slider {
            position: relative;
        }

        .cms-slider-track {
            display: flex;
            gap: 1.25rem;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            padding: .25rem .25rem 1rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .cms-slider-track::-webkit-scrollbar {
            display: none;
        }

        .cms-slide {
            flex: 0 0 calc(25% - 0.9375rem);
            min-width: 0;
            scroll-snap-align: start;
        }

        @media (max-width: 1023px) {
            .cms-slide {
                flex-basis: calc(50% - 0.625rem);
            }
        }

        @media (max-width: 639px) {
            .cms-slide {
                flex-basis: 86%;
            }
        }

        .cms-slide-card {
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .cms-slide-price {
            font-weight: 700;
            font-size: .95rem;
            margin-top: .35rem;
        }

        .cms-slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            background: var(--cms-surface);
            border: 1px solid color-mix(in oklab, var(--color-secondary) 25%, transparent);
            box-shadow: 0 4px 14px color-mix(in oklab, var(--color-secondary) 18%, transparent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cms-ink);
            cursor: pointer;
            z-index: 2;
            transition: opacity .15s ease, transform .15s ease;
        }

        .cms-slider-btn:hover {
            transform: translateY(-50%) scale(1.08);
        }

        .cms-slider-btn[disabled] {
            opacity: 0;
            pointer-events: none;
        }

        .cms-slider-btn svg {
            width: 1.15rem;
            height: 1.15rem;
        }

        .cms-slider-btn--prev { left: -1rem; }
        .cms-slider-btn--next { right: -1rem; }

        @media (max-width: 639px) {
            .cms-slider-btn--prev { left: .25rem; }
            .cms-slider-btn--next { right: .25rem; }
        }
    </style>
@endonce

<div class="cms-slider"
     x-data="{
         atStart: true,
         atEnd: true,
         update() {
             const el = this.$refs.track;
             this.atStart = el.scrollLeft <= 4;
             this.atEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 4;
         },
         scroll(dir) {
             const el = this.$refs.track;
             el.scrollBy({ left: dir * el.clientWidth, behavior: 'smooth' });
         }
     }"
     x-init="update(); $refs.track.addEventListener('scroll', () => update()); window.addEventListener('resize', () => update())">

    <button type="button"
            class="cms-slider-btn cms-slider-btn--prev"
            :disabled="atStart"
            @click="scroll(-1)"
            aria-label="Previous products">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>

    <div class="cms-slider-track" x-ref="track">
        @foreach($items as $item)
            <div class="cms-slide">
                <div class="cms-slide-card cms-product-card-small bg-white shadow-sm rounded-lg overflow-hidden hover:shadow-md transition-shadow group">
                    {{-- Image --}}
                    <div class="aspect-square overflow-hidden bg-gray-100">
                        @if($item['image'] ?? null)
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] ?? '' }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @endif
                    </div>

                    @if($item['tag'] ?? null)
                        <div class="absolute top-2 right-2">
                            <span class="cms-tag text-xs">{{ $item['tag'] }}</span>
                        </div>
                    @endif

                    {{-- Details --}}
                    <div class="p-4">
                        <h5 class="font-semibold text-sm mb-1 line-clamp-2">{{ $item['title'] ?? '' }}</h5>

                        @if($item['sku'] ?? null)
                            <p class="cms-muted text-xs mb-2">{{ $item['sku'] }}</p>
                        @endif

                        @if($showDescription && ($item['description'] ?? null))
                            <p class="text-xs cms-muted line-clamp-2">{{ $item['description'] }}</p>
                        @endif

                        @if($showPrice && ($item['price'] ?? null))
                            <p class="cms-slide-price">{{ $item['price'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <button type="button"
            class="cms-slider-btn cms-slider-btn--next"
            :disabled="atEnd"
            @click="scroll(1)"
            aria-label="Next products">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
</div>
