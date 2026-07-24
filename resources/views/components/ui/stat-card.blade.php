@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null,
])

<x-ui.card>

    <div class="flex justify-between items-start">

        <div>

            <div class="ui-stat-title">
                {{ $title }}
            </div>

            <div class="ui-stat-value">
                {{ $value }}
            </div>

            @if($trend)
                <div class="ui-stat-trend">
                    {{ $trend }}
                </div>
            @endif

        </div>

        @if($icon)
            <x-filament::icon
                :icon="$icon"
                class="w-8 h-8 text-gray-400"
            />
        @endif

    </div>

</x-ui.card>
