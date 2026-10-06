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
		<x-form.select wire:model='socialInfoForm.civilId'
			name="socialInfoForm.civilId"
			label="Civil Status"
			:selected="$socialInfoForm->civilId ?? ''"
			:options="$civilStatus ?? []"
			:required="true" />
	</div>
	<div class="col-12 col-md-4 col-lg-6 col-xl-4">
		<x-form.select wire:model='demographicsForm.nationalityId'
			name="demographicsForm.nationalityId"
			label="Nationality"
			:selected="$demographicsForm->nationalityId ?? ''"
			:options="$nationalities ?? []"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-4">
		<x-form.select wire:model='demographicsForm.religionId'
			name="demographicsForm.religionId"
			label="Religion"
			:selected="$demographicsForm->religionId ?? ''"
			:options="$religions ?? []"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-3">
		<x-form.select wire:model='socialInfoForm.educationId'
			name="socialInfoForm.educationId"
			label="Educational Attainment"
			:selected="$socialInfoForm->educationId ?? ''"
			:options="$educations ?? []" />
	</div>
	<div class="col-12 col-md-4 col-lg-6 col-xl-3">
		<x-form.input wire:model='socialInfoForm.philhealth'
			name="socialInfoForm.philhealth"
			label="PhilHealth ID" />
	</div>
	<div class="col-12 col-md-4 col-lg-6 col-xl-3">
		<x-form.input wire:model='socialInfoForm.skill'
			name="socialInfoForm.skills"
			label="Skills/Occupation" />
	</div>
	<div class="col-12 col-md-4 col-lg-4 col-xl-3">
		<x-form.input wire:model='socialInfoForm.income'
			name="socialInfoForm.income"
			label="Estimated Monthly Income" />
	</div>
</div>
