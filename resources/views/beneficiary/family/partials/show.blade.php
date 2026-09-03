<div class="d-flex flex-column gap-4">
	<div class="row">
		<div class="col-12 col-md-8 col-xl-4">
			<x-display-field label="Full Name"
				contents="{{ $member->name }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-display-field label="Sex"
				contents="{{ $member->sex->name }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-display-field label="Age"
				contents="{{ $member->age }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-display-field label="Civil Status"
				contents="{{ $member->civil->name }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-display-field label="Relationship"
				contents="{{ $member->relationship->name }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-4">
			<x-display-field label="Occupation"
				contents="{{ $member->occupation ?? 'N/A' }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-4">
			<x-display-field label="Income"
				contents="{{ '₱' . $member->income ?? 'N/A' }}" />
		</div>
	</div>
	<div class="d-flex justify-content-end gap-2">
		@can('update', $member)
			<a name=""
				id=""
				class="btn btn-sm btn-warning"
				href="{{ route('family.edit', $member) }}"
				role="button">
				<x-icon.font-awesome icon="arrow-up-right-from-square" />
				Edit Data
			</a>
		@endcan
	</div>
</div>
