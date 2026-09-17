<h4>Place of Birth</h4>
<div class="row">
	<div class="col-12 col-md-4 col-lg-4 col-xl-3">
		<x-form.input wire:model='form.houseNo'
			name="form.houseNo"
			label="House No."
			:required="true" />
	</div>
	<div class="col-12 col-md-8 col-lg-8 col-xl-4">
		<x-form.input wire:model='form.street'
			name="form.street"
			label="Street"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-3">
		<x-form.select wire:model='form.barangayId'
			name="form.barangayId"
			label="Barangay"
			:required="true"
			:selected="$form->barangayId ?? ''"
			:options="$barangays ?? []" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-2">
		<x-form.input name="city"
			label="City"
			type="text"
			:value="'Taguig City'"
			:readonly="true" />
	</div>
</div>
