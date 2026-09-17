<h4>Social Information</h4>
<div class="row">
	<div class="col-6 col-md-4 col-lg-3 col-xl-2">
		<x-form.input wire:model='form.contactNumber'
			name="form.contactNumber"
			label="Contact Number"
			type="text"
			:required="true" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-2">
		<x-form.select wire:model='form.civilId'
			name="form.civilId"
			label="Civil Status"
			:selected="$form->civilId ?? ''"
			:options="$civilStatus ?? []"
			:required="true" />
	</div>
	<div class="col-12 col-md-4 col-lg-6 col-xl-4">
		<x-form.select wire:model='form.nationalityId'
			name="form.nationalityId"
			label="Nationality"
			:selected="$form->nationalityId ?? ''"
			:options="$nationalities ?? []"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-4">
		<x-form.select wire:model='form.religionId'
			name="form.religionId"
			label="Religion"
			:selected="$form->religionId ?? ''"
			:options="$religions ?? []"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-3">
		<x-form.select wire:model='form.educationId'
			name="form.educationId"
			label="Educational Attainment"
			:selected="$form->educationId ?? ''"
			:options="$educations ?? []" />
	</div>
	<div class="col-12 col-md-4 col-lg-6 col-xl-3">
		<x-form.input wire:model='form.philhealth'
			name="form.philhealth"
			label="PhilHealth ID" />
	</div>
	<div class="col-12 col-md-4 col-lg-6 col-xl-3">
		<x-form.input wire:model='form.skill'
			name="form.skills"
			label="Skills/Occupation" />
	</div>
	<div class="col-12 col-md-4 col-lg-4 col-xl-3">
		<x-form.input wire:model='form.income'
			name="form.income"
			label="Estimated Monthly Income" />
	</div>
</div>
