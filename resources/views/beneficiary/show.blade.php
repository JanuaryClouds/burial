@extends('layouts.app')
@section('content')
	<x-card>
		<x-slot:header>{{ $beneficiary->fullname() }}</x-slot:header>
		<livewire:beneficiary.show :beneficiary="$beneficiary" />
		<x-slot:footer>
			<a href="{{ route('beneficiary.index') }}"
				class="btn btn-sm btn-light">
				<i class="fa-solid fa-arrow-left"></i>
				Back
			</a>
			@can('update', [\App\Models\Beneficiary::class, $beneficiary])
				<a href="{{ route('beneficiary.edit', $beneficiary) }}"
					class="btn btn-sm btn-light">
					<i class="fa-solid fa-pencil"></i>
					Edit
				</a>
			@endcan
		</x-slot:footer>
	</x-card>

	<x-card>
		<x-slot:header>Family Composition</x-slot:header>
		<livewire:beneficiary.family.index :beneficiary="$beneficiary" />
	</x-card>
@endsection
