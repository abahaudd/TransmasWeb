{{-- FAQ accordion block. Expects $data: kicker, heading, intro, items[{question,answer}] --}}
<section class="max-w-7xl mx-auto px-8 lg:px-16 pt-16 pb-24">
    @if(!empty($data['kicker']))
        <div class="flex items-center gap-3 mb-6">
            <div class="cms-kicker-line"></div>
            <span class="cms-kicker">{{ $data['kicker'] }}</span>
        </div>
    @endif
    @if(!empty($data['heading']))
        <h1 class="cms-display text-[clamp(2rem,4vw,3rem)] leading-tight mb-6">{{ $data['heading'] }}</h1>
    @endif
    @if(!empty($data['intro']))
        <p class="cms-body-text max-w-2xl mb-12 text-[0.95rem]">{{ $data['intro'] }}</p>
    @endif

    <div class="flex flex-col gap-3 max-w-3xl">
        @foreach($data['items'] ?? [] as $item)
            <div class="cms-card cms-card--bordered" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-6 p-6 text-left">
                    <span class="cms-text text-[0.95rem] font-medium">{{ $item['question'] ?? '' }}</span>
                    <i data-lucide="chevron-down" size="16" class="cms-icon-gold cms-accordion-icon shrink-0" :class="open && 'cms-accordion-icon--open'"></i>
                </button>
                <div x-show="open" x-transition.opacity.duration.200ms class="cms-prose px-6 pb-6" x-cloak>
                    {{ $item['answer'] ?? '' }}
                </div>
            </div>
        @endforeach
    </div>
</section>
