@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'ui-card']) }}>

    @if($title)
        <div class="ui-card-title">
            {{ $title }}
        </div>
    @endif

    <div class="ui-card-body">
        {{ $slot }}
    </div>

</div>
