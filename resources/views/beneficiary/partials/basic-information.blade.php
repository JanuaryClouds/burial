<h4>Basic Information</h4>
<div class="row">
	<div class="col-12 col-md-6 col-lg-4">
		<x-form.input wire:model='form.firstName'
			name="form.firstName"
			label="First Name"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-3">
		<x-form.input wire:model='form.middleName'
			name="form.middleName"
			label="Middle Name" />
	</div>
	<div class="col-8 col-md-6 col-lg-3">
		<x-form.input wire:model='form.lastName'
			name="form.lastName"
			label="Last Name"
			:required="true" />
	</div>
	<div class="col-4 col-md-6 col-lg-2">
		<x-form.input wire:model='form.suffix'
			name="form.suffix"
			label="Suffix" />
	</div>
	<div class="col-12 col-md-4 col-lg-2 col-xl-2">
		<x-form.select wire:model='form.sexId'
			name="form.sexId"
			label="Sex"
			:selected="$form->sexId ?? ''"
			:options="$genders ?? []"
			:required="true" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-3">
		<x-form.input wire:model.live.blur='form.dateOfBirth'
			name="form.dateOfBirth"
			label="Date of Birth"
			:required="true"
			:max="now()"
			type="date" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-3">
		<x-form.input wire:model.live.blur='form.dateOfDeath'
			name="form.dateOfDeath"
			label="Date of Death"
			:required="true"
			:max="now()"
			type="date" />
	</div>
	<div class="col-12 col-md-12 col-lg-6 col-xl-4 d-flex gap-4">
		@if (
			$form->dateOfBirth &&
				$form->dateOfDeath &&
				\Carbon\Carbon::parse($form->dateOfBirth)->diffInHours(\Carbon\Carbon::parse($form->dateOfDeath)) < 24)
			<x-form.check wire:model='form.lethal'
				name="form.lethal"
				label="Lethal"
				:checked="$form->lethal" />
		@endif
		<x-form.check wire:model='form.pwd'
			name="form.pwd"
			label="Person with Disability"
			:checked="$form->pwd" />
	</div>
</div>
