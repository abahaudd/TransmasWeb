{{--
  Product Card - Small (4 per row)
  Compact cards for grid display, 4 cards fit in one row
  Usage: Product grid displays, sale sections
--}}

@props(['items' => [], 'showDescription' => false])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
    @foreach($items as $item)
        <div class="cms-product-card-small bg-white shadow-sm rounded-lg overflow-hidden hover:shadow-md transition-shadow group">
            {{-- Image --}}
            <div class="aspect-square overflow-hidden bg-gray-100">
                @if($item['image'] ?? null)
                    <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                @endif

                @if($item['tag'] ?? null)
                    <div class="absolute top-2 right-2">
                        <span class="cms-tag text-xs">{{ $item['tag'] }}</span>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="p-4">
                <h5 class="font-semibold text-sm mb-1 line-clamp-2">{{ $item['title'] }}</h5>

                @if($item['sku'] ?? null)
                    <p class="cms-muted text-xs mb-2">{{ $item['sku'] }}</p>
                @endif

                @if($showDescription && ($item['description'] ?? null))
                    <p class="text-xs cms-muted line-clamp-2">{{ $item['description'] }}</p>
                @endif
            </div>
        </div>
    @endforeach
</div>
