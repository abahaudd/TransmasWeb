{{-- Hero block. Expects $data: kicker, heading (HTML), body, buttons[{label,url,style}], bg_image, side_image, side_kicker, side_title, side_meta, pills[{label}] --}}
@php
    $text = static function ($value, string $fallback = ''): string {
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

    $img = fn ($path) => \Illuminate\Support\Str::startsWith($path ?? '', ['http://', 'https://']) ? $path : asset($path ?? '');
    $kicker = $text($data['kicker'] ?? null, '');
    $heading = $text($data['heading'] ?? null, '');
    $body = $text($data['body'] ?? null, '');
    $pillsLabel = $text($data['pills_label'] ?? null, '');
    $sideTitle = $text($data['side_title'] ?? null, '');
    $sideKicker = $text($data['side_kicker'] ?? null, '');
    $sideMeta = $text($data['side_meta'] ?? null, '');
    $pills = collect($data['pills'] ?? [])->pluck('label')->filter()->values();
@endphp
<section class="cms-hero relative flex items-center" x-data="{ activePill: '{{ $pills[1] ?? $pills[0] ?? '' }}' }">
    <!-- BG image -->
    <div class="absolute inset-0 overflow-hidden">
        @if(!empty($data['bg_image']))
            <img src="{{ $img($data['bg_image']) }}" alt="" class="w-full h-full object-cover opacity-20" style="object-position: center 30%;">
        @endif
        <div class="cms-hero-overlay absolute inset-0"></div>
    </div>

    <!-- decorative gold line -->
    <div class="cms-gold-vline absolute left-0 top-1/2 -translate-y-1/2 hidden lg:block"></div>

    <div class="relative z-10 w-full max-w-7xl mx-auto px-8 lg:px-16 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center py-24">
        <div>
            @if($kicker !== '')
                <div class="flex items-center gap-3 mb-8">
                    <div class="cms-kicker-line" style="width: 32px;"></div>
                    <span class="cms-kicker">{{ $kicker }}</span>
                </div>
            @endif
            <h1 class="cms-display cms-h1 mb-6">
                {!! $heading !!}
            </h1>
            @if($body !== '')
                <p class="cms-body-text mb-10 max-w-lg text-[1rem]">{{ $body }}</p>
            @endif
            <div class="flex flex-wrap gap-4">
                @foreach($data['buttons'] ?? [] as $btn)
                    @php
                        $btnStyle = $text($btn['style'] ?? null, 'primary');
                        $btnUrl = $text($btn['url'] ?? null, '#');
                        $btnLabel = $text($btn['label'] ?? null, '');
                    @endphp
                    @if($btnStyle === 'primary')
                        <a href="{{ $btnUrl }}" class="cms-btn cms-btn--primary group">
                            {{ $btnLabel }} <i data-lucide="arrow-right" size="15" class="group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    @else
                        <a href="{{ $btnUrl }}" class="cms-btn cms-btn--outline">
                            {{ $btnLabel }}
                        </a>
                    @endif
                @endforeach
            </div>

            @if($pills->isNotEmpty())
                <!-- Filter pills (interactive) -->
                <div class="flex items-center gap-3 mt-10 flex-wrap">
                    @if($pillsLabel !== '')
                        <span class="cms-muted text-[0.72rem] tracking-[0.15em] uppercase">{{ $pillsLabel }}:</span>
                    @endif
                    @foreach($pills as $p)
                        <button @click="activePill = '{{ $p }}'"
                                class="cms-pill"
                                :class="activePill === '{{ $p }}' && 'cms-pill--active'">
                            {{ $p }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Hero right image panel -->
        @if(!empty($data['side_image']))
            <div class="hidden lg:block relative">
                <div class="relative overflow-hidden" style="aspect-ratio: 3/4; max-height: 580px;">
                    <img src="{{ $img($data['side_image']) }}" alt="{{ $sideTitle }}" class="w-full h-full object-cover brightness-90 contrast-105">
                    <div class="cms-overlay-bottom-soft absolute inset-0"></div>
                    @if($sideTitle !== '' || $sideKicker !== '')
                        <div class="cms-glass absolute bottom-6 left-6 right-6 p-5 backdrop-blur-md">
                            <div class="cms-kicker text-[0.68rem] tracking-[0.2em]">{{ $sideKicker }}</div>
                            <div class="cms-display text-[1rem] font-normal">{{ $sideTitle }}</div>
                            <div class="cms-muted text-[0.8rem] mt-1">{{ $sideMeta }}</div>
                        </div>
                    @endif
                </div>
                <div class="cms-corner-tr absolute -top-3 -right-3 w-16 h-16"></div>
                <div class="cms-corner-bl absolute -bottom-3 -left-3 w-16 h-16"></div>
            </div>
        @endif
    </div>

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-40">
        <span class="cms-kicker text-[0.65rem] tracking-[0.2em]">Scroll</span>
        <i data-lucide="chevron-down" size="14" class="cms-icon-gold"></i>
    </div>
</section>
