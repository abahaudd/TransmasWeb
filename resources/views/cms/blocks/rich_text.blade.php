{{-- Rich text block for generic content pages (about, policies, ...). Expects $data: kicker, heading, body (HTML) --}}
{{-- Same container as the home page sections --}}
<section class="max-w-7xl mx-auto px-8 lg:px-16 pt-16 pb-24">
    @if(!empty($data['kicker']))
        <div class="flex items-center gap-3 mb-6">
            <div class="cms-kicker-line"></div>
            <span class="cms-kicker">{{ $data['kicker'] }}</span>
        </div>
    @endif
    @if(!empty($data['heading']))
        <h1 class="cms-display text-[clamp(2rem,4vw,3rem)] leading-tight mb-8">{{ $data['heading'] }}</h1>
    @endif
    <div class="cms-prose">
        {!! $data['body'] ?? '' !!}
    </div>
</section>
