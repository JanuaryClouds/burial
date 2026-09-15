<h4>Basic Information</h4>
<div class="row">
	<div class="col-12 col-md-6 col-lg-4">
		<x-form.input wire:model='firstName'
			name="firstName"
			label="First Name"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-3">
		<x-form.input wire:model='middleName'
			name="middleName"
			label="Middle Name" />
	</div>
	<div class="col-8 col-md-6 col-lg-3">
		<x-form.input wire:model='lastName'
			name="lastName"
			label="Last Name"
			:required="true" />
	</div>
	<div class="col-4 col-md-6 col-lg-2">
		<x-form.input wire:model='suffix'
			name="suffix"
			label="Suffix" />
	</div>
	<div class="col-12 col-md-4 col-lg-2 col-xl-2">
		<x-form.select wire:model='sexId'
			name="sexId"
			label="Sex"
			:selected="$sexId ?? ''"
			:options="$genders ?? []"
			:required="true" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-3">
		<x-form.input wire:model.live.blur='dateOfBirth'
			name="dateOfBirth"
			label="Date of Birth"
			:required="true"
			:max="now()"
			type="date" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-3">
		<x-form.input wire:model.live.blur='dateOfDeath'
			name="dateOfDeath"
			label="Date of Death"
			:required="true"
			:max="now()"
			type="date" />
	</div>
	<div class="col-12 col-md-12 col-lg-6 col-xl-4 d-flex gap-4">
		@if (
			$dateOfBirth &&
				$dateOfDeath &&
				\Carbon\Carbon::parse($dateOfBirth)->diffInHours(\Carbon\Carbon::parse($dateOfDeath)) < 24)
			<x-form.check wire:model='lethal'
				name="lethal"
				label="Lethal"
				:checked="$lethal" />
		@endif
		<x-form.check wire:model='pwd'
			name="pwd"
			label="Person with Disability"
			:checked="$pwd" />
	</div>
</div>
