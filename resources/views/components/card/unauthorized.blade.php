<x-card>
	<x-slot:header>{{ $header }}</x-slot:header>
	<div class="d-flex flex-center flex-column gap-4 text-gray-400">
		<i class="fa-solid fa-lock fs-4"></i>
		<p class="fs-5 fw-semibold">
			You do not have permissions to view this
		</p>
	</div>
	<x-slot:footer>
		<small class="text-muted">
			Actions are disabled
		</small>
	</x-slot:footer>
</x-card>
