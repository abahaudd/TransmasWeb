{{-- Numbered steps block. Expects $data: anchor, kicker, heading, items[{icon,title,body}] --}}
<section id="{{ $data['anchor'] ?? 'how-it-works' }}" class="cms-section-dark py-28">
    <div class="max-w-7xl mx-auto px-8 lg:px-16">
        <div class="text-center mb-16">
            @if(!empty($data['kicker']))
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="cms-kicker-line"></div>
                    <span class="cms-kicker">{{ $data['kicker'] }}</span>
                    <div class="cms-kicker-line"></div>
                </div>
            @endif
            <h2 class="cms-display cms-h2">{{ $data['heading'] ?? '' }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-0">
            @foreach($data['items'] ?? [] as $idx => $step)
                <div class="relative p-8 {{ $idx > 0 ? 'border-l cms-divider-soft' : '' }}">
                    <div class="cms-display text-right mb-6 text-5xl leading-none" style="color: var(--cms-gold-border-soft);">{{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="cms-icon-box w-10 h-10 flex items-center justify-center mb-5">
                        <i data-lucide="{{ $step['icon'] ?? 'star' }}" size="16" class="cms-icon-gold"></i>
                    </div>
                    <h3 class="cms-display cms-h3 mb-2">{{ $step['title'] ?? '' }}</h3>
                    <p class="cms-muted text-[0.85rem] leading-relaxed">{{ $step['body'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
