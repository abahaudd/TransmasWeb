@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'ui-section']) }}>
    @if($title || $description)
        <div class="ui-section-head">
            @if($title)
                <h2 class="ui-section-title">{{ $title }}</h2>
            @endif
            @if($description)
                <p class="ui-section-description">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="ui-section-body">
        {{ $slot }}
    </div>
</section>
