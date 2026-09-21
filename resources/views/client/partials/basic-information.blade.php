<h4>Basic Information</h4>
<div class="row">
	<div class="col-12 col-md-6 col-lg-4">
		<x-form.input name='firstName'
			label="First Name"
			value="{{ Auth::user()->first_name }}"
			:readonly="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-3">
		<x-form.input name='middleName'
			value="{{ Auth::user()->middle_name ?? null }}"
			:readonly="true"
			label="Middle Name" />
	</div>
	<div class="col-8 col-md-6 col-lg-3">
		<x-form.input name='lastName'
			label="Last Name"
			value="{{ Auth::user()->last_name }}"
			:readonly="true" />
	</div>
	<div class="col-4 col-md-6 col-lg-2">
		<x-form.input name='suffix'
			value="{{ Auth::user()->suffix ?? '' }}"
			:readonly="true"
			label="Suffix" />
	</div>
</div>
<div class="row">
	<div class="col-6 col-md-3 col-lg-3 col-xl-2">
		<x-form.input wire:model='form.dateOfBirth'
			name="form.dateOfBirth"
			label="Date of Birth"
			:required="true"
			max="{{ now() }}"
			type="date" />
	</div>
	<div class="col-6 col-md-3 col-lg-2 col-xl-2">
		<x-form.select wire:model='form.sexId'
			name="form.sexId"
			label="Sex"
			:selected="$form->sexId ?? ''"
			:options="$genders ?? []"
			:required="true" />
	</div>
</div>
