<i class="ki-duotone ki-{{ $icon }} fs-{{ $size }}">
	@for ($i = 1; $i <= $pathsCount; $i++)
		<span class="path{{ $i }}"></span>
	@endfor
</i>
