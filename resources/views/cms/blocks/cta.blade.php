{{-- Call-to-action block. Expects $data: anchor, kicker, heading (HTML), body, button_label, button_url, footnote --}}
<section id="{{ $data['anchor'] ?? 'apply' }}" class="relative py-28 overflow-hidden">
    <div class="cms-cta-bg absolute inset-0"></div>
    <div class="cms-cta-glow absolute top-0 right-0 w-96 h-96 rounded-full opacity-5 blur-[80px]"></div>
    <div class="relative z-10 max-w-3xl mx-auto px-8 text-center">
        @if(!empty($data['kicker']))
            <div class="flex items-center justify-center gap-3 mb-6">
                <div class="cms-kicker-line"></div>
                <span class="cms-kicker">{{ $data['kicker'] }}</span>
                <div class="cms-kicker-line"></div>
            </div>
        @endif
        <h2 class="cms-display text-[clamp(2rem,4vw,3rem)] leading-tight mb-4">{!! $data['heading'] ?? '' !!}</h2>
        @if(!empty($data['body']))
            <p class="cms-muted text-[0.95rem] leading-relaxed mb-10">{{ $data['body'] }}</p>
        @endif
        <div class="flex justify-center">
            <a href="{{ $data['button_url'] ?? '/contact' }}" class="cms-btn cms-btn--primary justify-center">
                {{ $data['button_label'] ?? 'Get in Touch' }} <i data-lucide="arrow-right" size="14"></i>
            </a>
        </div>
        @if(!empty($data['footnote']))
            <p class="cms-muted text-[0.72rem] mt-4 tracking-wide">{{ $data['footnote'] }}</p>
        @endif
    </div>
</section>
