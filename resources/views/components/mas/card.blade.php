<div {{ $attributes->merge(['class'=>'mas-card']) }}>
@if(isset($title))<h3>{{ $title }}</h3>@endif
{{ $slot }}
</div>