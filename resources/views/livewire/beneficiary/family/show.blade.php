<div class="d-flex flex-column gap-4 w-100 border border-2 border-dashed border-gray-200 rounded py-3 px-4">
	<div class="row">
		<div class="col-12 col-md-8 col-lg-5">
			<x-form.display :contents="$member->name"
				:label="'Name'" />
		</div>
		<div class="col-12 col-md-4 col-lg-3">
			<x-form.display :contents="$member->age"
				:label="'Age'" />
		</div>
		<div class="col-6 col-md-3 col-lg-2">
			<x-form.display :contents="$member->civil->name"
				:label="'Civil Status'" />
		</div>
		<div class="col-6 col-md-3 col-lg-2">
			<x-form.display :contents="$member->sex->name"
				:label="'Sex'" />
		</div>
		<div class="col-12 col-md-6 col-xl-3">
			<x-form.display :contents="$member->relationship->name"
				:label="'Relationship to the Beneficiary'" />
		</div>
		<div class="col-12 col-md-6 col-xl-4">
			<x-form.display :contents="$member->occupation ?? 'N/A'"
				:label="'Occupation'" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-form.display :contents="$member->income ?? 'N/A'"
				:label="'income'" />
		</div>
	</div>
	<div class="d-flex justify-content-end align-items-center gap-2">
		@can('delete', [\App\Models\BeneficiaryFamily::class, $member->load(['beneficiary.application'])])
			<x-button wire:click="$parent.removeFamilyMember('{{ $member->uuid }}')"
				wire:loading.attr="disabled"
				class="btn-sm btn-warning">
				<x-icon.font-awesome class="fa-trash-can" />
				Remove Family Member
			</x-button>
		@endcan
		@can('update', [\App\Models\BeneficiaryFamily::class, $member->load(['beneficiary.application'])])
			<a href="{{ route('beneficiary.family.edit', $member) }}"
				class="btn btn-sm btn-light">
				<i class="fa-solid fa-pencil"></i>
				Edit
			</a>
		@endcan
	</div>
</div>
