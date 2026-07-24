{{-- Testimonials block. Expects $data: anchor, kicker, heading, items[{quote,name,title,city,rating}] --}}
<section id="{{ $data['anchor'] ?? 'about' }}" class="cms-section-dark py-28">
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($data['items'] ?? [] as $t)
                <div class="cms-card cms-card--bordered p-8 flex flex-col">
                    <div class="flex gap-1 mb-6">
                        @for($i = 0; $i < min((int) ($t['rating'] ?? 5), 5); $i++)
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="cms-icon-gold" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        @endfor
                    </div>
                    <blockquote class="cms-display text-[0.97rem] italic leading-relaxed flex-1 mb-6 font-normal">"{{ $t['quote'] ?? '' }}"</blockquote>
                    <div><div class="cms-gold-light text-[0.85rem] font-medium">{{ $t['name'] ?? '' }}</div><div class="cms-muted text-[0.75rem] mt-0.5">{{ $t['title'] ?? '' }}@if(!empty($t['city'])) · {{ $t['city'] }}@endif</div></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
