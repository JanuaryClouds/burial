@props(['member' => [], 'readonly' => true])
<div class="row">
	<div class="col-12 col-md-12 col-lg-8">
		<x-form-input name="fam_name"
			label="Name"
			value="{{ $member->name ?? null }}"
			required="true"
			:readonly="$readonly" />
	</div>
	<div class="col-12 col-md-6 col-lg-2">
		<x-form-select name="fam_sex_id"
			label="Sex"
			:options="$genders"
			selected="{{ $member->sex_id ?? null }}"
			required="true"
			:disabled="$readonly" />
	</div>
	<div class="col-12 col-md-6 col-lg-2">
		<x-form-input name="fam_age"
			label="Age"
			type="number"
			value="{{ $member->age ?? null }}"
			required="true"
			:readonly="$readonly" />
	</div>
</div>
<div class="row">
	<div class="col-6 col-md-6 col-lg-3">
		<x-form-select name="fam_civil_id"
			label="Civil Status"
			:options="$civilStatus"
			selected="{{ $member->civil_id ?? null }}"
			required="true"
			:disabled="$readonly" />
	</div>
	<div class="col-6 col-md-6 col-lg-3">
		<x-form-select name="fam_relationship_id"
			label="Relationship"
			:options="$relationships"
			selected="{{ $member->relationship_id ?? null }}"
			required="true"
			:disabled="$readonly" />
	</div>
	<div class="col-6 col-md-6 col-lg-3">
		<x-form-input name="fam_occupation"
			label="Occupation"
			value="{{ $member->occupation ?? null }}"
			:readonly="$readonly" />
	</div>
	<div class="col-6 col-md-6 col-lg-3">
		<x-form-input name="fam_income"
			label="Income"
			value="{{ $member->income ?? null }}"
			:readonly="$readonly" />
	</div>
</div>
<div class="d-flex justify-content-end">
	@can('edit', $member)
		@if (!Route::is('family.edit') && !Route::is('application.create'))
			@if (Route::is('family.show'))
				<a name=""
					id=""
					class="btn btn-sm btn-warning"
					href="{{ route('family.edit', $member) }}"
					role="button">
					<i class="fa-solid fa-arrow-up-right-from-square"></i>
					Edit Data
				</a>
			@else
				<a name=""
					id=""
					class="btn btn-info btn-sm"
					href="{{ route('family.show', $member) }}"
					role="button">
					<i class="fa-solid fa-arrow-up-right-from-square"></i>
					View Family Member
				</a>
			@endif
		@endif
	@endcan
</div>
