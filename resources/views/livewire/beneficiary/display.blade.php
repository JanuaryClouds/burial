<x-card>
	<x-slot:header>
		@if ($beneficiary)
			<i class="fa-solid fa-check-circle me-2 fs-4 text-success"></i>
			Selected Beneficiary: {{ $beneficiary->fullname() }}
		@else
			<i class="fa-solid fa-question-circle me-2 fs-4 text-warning"></i>
			No Beneficiary Selected
		@endif
	</x-slot:header>
	@if ($beneficiary)
		<h4 class="card-title">Beneficiary's Information</h4>
		@include('beneficiary.partials.create.form', [
			'beneficiary' => $beneficiary,
			'readonly' => true,
		])
		<div class="separator my-4"></div>
		<h4 class="card-title">Family Composition</h4>
		@foreach ($family as $member)
			@include('beneficiary.family.partials.show', [
				'member' => $member,
				'readonly' => true,
			])
		@endforeach
	@endif
	<x-slot:footer>
		<a name=""
			id=""
			class="btn btn-light btn-sm"
			href="{{ route('beneficiary.create') }}"
			role="button">
			<i class="fa-solid fa-plus"></i>
			Create Another Beneficiary Record
		</a>
		@if ($beneficiary && auth()->user()->can('edit', $beneficiary))
			<a name=""
				id=""
				class="btn btn-warning btn-sm"
				href="{{ route('beneficiary.edit', $beneficiary) }}"
				role="button">
				<i class="fa-solid fa-arrow-up-right-from-square"></i>
				Edit
			</a>
		@endif
	</x-slot:footer>
</x-card>
