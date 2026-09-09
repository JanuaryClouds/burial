<x-card>
	@isset($header)
		<x-slot:header>{{ $header }}</x-slot:header>
	@endisset
	<div class="d-flex flex-center flex-column gap-4 text-gray-400">
		{{ $slot }}
	</div>
	<x-slot:footer>
		<small class="text-muted">
			Actions are disabled
		</small>
	</x-slot:footer>
</x-card>
