{{-- Feature callout block. Expects $data: kicker, heading (HTML), body, image, bullets[{title,subtitle}], button_label, button_url --}}
@php
    $img = fn ($path) => \Illuminate\Support\Str::startsWith($path ?? '', ['http://', 'https://']) ? $path : asset($path ?? '');
@endphp
<section class="max-w-7xl mx-auto px-8 lg:px-16 py-24">
    <div class="cms-frame grid grid-cols-1 lg:grid-cols-2 gap-0 overflow-hidden">
        <div class="relative overflow-hidden min-h-[400px]">
            @if(!empty($data['image']))
                <img src="{{ $img($data['image']) }}" alt="" class="w-full h-full object-cover brightness-75">
            @endif
            <div class="cms-overlay-right absolute inset-0"></div>
        </div>
        <div class="cms-card flex flex-col justify-center p-12">
            @if(!empty($data['kicker']))
                <div class="flex items-center gap-3 mb-6">
                    <div class="cms-kicker-line" style="width: 20px;"></div>
                    <span class="cms-kicker text-[0.68rem]">{{ $data['kicker'] }}</span>
                </div>
            @endif
            <h2 class="cms-display cms-h2-sm leading-tight mb-6">{!! $data['heading'] ?? '' !!}</h2>
            @if(!empty($data['body']))
                <p class="cms-muted text-[0.9rem] leading-relaxed mb-8">{{ is_string($data['body']) ? $data['body'] : ($data['body'][0] ?? '') }}</p>
            @endif
            @if(!empty($data['bullets']))
                <div class="grid grid-cols-2 gap-5 mb-8">
                    @foreach($data['bullets'] as $bullet)
                        <div><div class="flex gap-3"><div class="cms-badge-gold" style="width:4px; height:4px; margin-top:8px;"></div><div><div class="cms-text text-[0.82rem] font-medium">{{ $bullet['title'] ?? '' }}</div><div class="cms-muted text-[0.75rem]">{{ $bullet['subtitle'] ?? '' }}</div></div></div></div>
                    @endforeach
                </div>
            @endif
            @if(!empty($data['button_label']))
                <a href="{{ $data['button_url'] ?? '#' }}" class="cms-btn cms-btn--outline cms-btn--sm self-start">{{ $data['button_label'] }} <i data-lucide="arrow-right" size="13"></i></a>
            @endif
        </div>
    </div>
</section>
