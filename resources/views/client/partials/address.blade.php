<h4>Address</h4>
<div class="row">
	<div class="col-5 col-md-3 col-lg-3 col-xl-2">
		<x-form.input wire:model='form.houseNo'
			name="form.houseNo"
			label="House Number"
			:required="true" />
	</div>
	<div class="col-7 col-md-5 col-lg-5 col-xl-5">
		<x-form.input wire:model='form.street'
			name="form.street"
			label="Street"
			:required="true" />
	</div>
	<div class="col-12 col-md-4 col-lg-4 col-xl-3">
		<x-form.select wire:model='form.barangayId'
			name="form.barangayId"
			label="Barangay"
			:selected="$form->barangayId ?? ''"
			:options="$barangays ?? []"
			:required="true" />
	</div>
	{{-- <div class="col-4 col-md-2 col-lg-2 col-xl-1">
        <input type="hidden"
            wire:model='districtId'
            name="districtId"
            id="districtId" />
        <x-form.input id="districtId_display"
            name="districtId_display"
            label="District"
            :readonly="true" />
    </div> --}}
	<div class="col-12 col-lg-3 col-xl-2">
		<x-form.input name="city"
			label="City"
			type="text"
			:value="'Taguig City'"
			:readonly="true" />
	</div>
</div>
