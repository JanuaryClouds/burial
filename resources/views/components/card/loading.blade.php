<x-card>
	@isset($header)
		<x-slot:header>{{ $header }}</x-slot:header>
	@endisset
	<div class="d-flex flex-center w-full h-250px">
		<x-icon.font-awesome class="spinner spinner-border fs-4" />
	</div>
	@isset($footer)
		<x-slot:footer>{{ $footer }}</x-slot:footer>
	@endisset
</x-card>
