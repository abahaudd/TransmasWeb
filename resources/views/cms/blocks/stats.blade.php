{{-- Stats bar block. Expects $data: items[{value,label}] --}}
<section class="cms-section-gold">
    <div class="max-w-7xl mx-auto px-8 py-10 grid grid-cols-2 lg:grid-cols-4 gap-8">
        @foreach($data['items'] ?? [] as $s)
            <div class="text-center">
                <div class="cms-display text-4xl lg:text-5xl leading-tight" style="color: var(--cms-ink);">{{ $s['value'] ?? '' }}</div>
                <div class="cms-muted text-[0.72rem] tracking-[0.12em] mt-1.5 uppercase">{{ $s['label'] ?? '' }}</div>
            </div>
        @endforeach
    </div>
</section>
