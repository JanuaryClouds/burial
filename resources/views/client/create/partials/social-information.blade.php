<h4>Social Information</h4>
<div class="row">
	<div class="col-6 col-md-4 col-lg-3 col-xl-2">
		<x-form.input wire:model='contactNumber'
			name="contactNumber"
			label="Contact Number"
			type="text"
			:required="true" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-2">
		<x-form.select wire:model='civilId'
			name="civilId"
			label="Civil Status"
			:selected="$civilId ?? ''"
			:options="$civilStatus ?? []"
			:required="true" />
	</div>
	<div class="col-12 col-md-4 col-lg-6 col-xl-4">
		<x-form.select wire:model='nationalityId'
			name="nationalityId"
			label="Nationality"
			:selected="$nationalityId ?? ''"
			:options="$nationalities ?? []"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-4">
		<x-form.select wire:model='religionId'
			name="religionId"
			label="Religion"
			:selected="$religionId ?? ''"
			:options="$religions ?? []"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-3">
		<x-form.select wire:model='educationId'
			name="educationId"
			label="Educational Attainment"
			:selected="$educationId ?? ''"
			:options="$educations ?? []" />
	</div>
	<div class="col-12 col-md-4 col-lg-6 col-xl-3">
		<x-form.input wire:model='philhealth'
			name="philhealth"
			label="PhilHealth ID" />
	</div>
	<div class="col-12 col-md-4 col-lg-6 col-xl-3">
		<x-form.input wire:model='skill'
			name="skills"
			label="Skills/Occupation" />
	</div>
	<div class="col-12 col-md-4 col-lg-4 col-xl-3">
		<x-form.input wire:model='income'
			name="income"
			label="Estimated Monthly Income" />
	</div>
</div>
