@props([
    'extraClasses' => '',
])
<div class="row gap-y-2 gap-md-0 {{ $extraClasses }}">
	{{ $slot }}
</div>
