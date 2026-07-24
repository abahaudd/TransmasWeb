{{--
  Product Card - Half Width (Right Side)
  Displays product card on right half of page, stacked vertically (2 rows)
  Usage: Asymmetric layout with content on left, cards stacked on right
--}}

@props(['items' => []])

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left content area (can be filled by parent) --}}
    <div class="lg:col-span-1">
        {{ $slot ?? null }}
    </div>

    {{-- Right side - 2 stacked product cards --}}
    <div class="lg:col-span-2 space-y-6">
        @foreach($items as $item)
            <div class="cms-product-card-half bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-0">
                    {{-- Image --}}
                    <div class="aspect-video sm:aspect-square overflow-hidden bg-gray-100">
                        @if($item['image'] ?? null)
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="p-6 flex flex-col justify-between">
                        @if($item['tag'] ?? null)
                            <span class="cms-tag text-xs mb-3">{{ $item['tag'] }}</span>
                        @endif

                        <div>
                            <h4 class="cms-h3 mb-1">{{ $item['title'] }}</h4>

                            @if($item['sku'] ?? null)
                                <p class="cms-muted text-xs mb-3">{{ $item['sku'] }}</p>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
