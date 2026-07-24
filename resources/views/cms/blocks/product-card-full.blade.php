{{--
  Product Card - Full Width
  Displays a single product across full page width
  Usage: Hero-style product showcase with image, details, and CTA
--}}

@props(['data'])

<div class="cms-product-card-full bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
        {{-- Product Image --}}
        <div class="aspect-square overflow-hidden bg-gray-100">
            @if($data['image'] ?? null)
                <img src="{{ $data['image'] }}" alt="{{ $data['title'] }}"
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
            @endif
        </div>

        {{-- Product Details --}}
        <div class="p-8 md:p-12 flex flex-col justify-between">
            @if($data['tag'] ?? null)
                <div class="inline-flex w-fit mb-4">
                    <span class="cms-tag">{{ $data['tag'] }}</span>
                </div>
            @endif

            <div>
                <h3 class="cms-h2 mb-2">{{ $data['title'] }}</h3>

                @if($data['sku'] ?? null)
                    <p class="cms-muted text-sm mb-4">{{ $data['sku'] }}</p>
                @endif

                @if($data['description'] ?? null)
                    <p class="cms-body-text mb-6">{{ $data['description'] }}</p>
                @endif

            </div>
        </div>
    </div>
</div>
