<h4>Place of Birth</h4>
<div class="row">
	<div class="col-12 col-lg-6">
		<x-callout class="bg-info-subtle border-info text-info">
			<x-slot:icon>
				<x-icon.font-awesome class="fs-2 text-info fa-info-circle" />
			</x-slot:icon>
			<x-slot:title>
				<p class="fs-6 fw-bold">About Provinces/Cities and Municipalities</p>
			</x-slot:title>
			Municipalities are separated from Provinces/Cities. If you currently reside in a municipality, select your
			municipality
			from the dropdown. If you currently reside in a city, select your city from the dropdown.
		</x-callout>
	</div>
	<div class="col-12 col-lg-6">
		<x-callout class="bg-info-subtle border-info text-info">
			<x-slot:icon>
				<x-icon.font-awesome class="fs-2 text-info fa-info-circle" />
			</x-slot:icon>
			<x-slot:title>
				<p class="fs-6 fw-bold">Start from Region</p>
			</x-slot:title>
			Start selecting from the Region dropdown. This will filter the options in the Province/City and Municipality
			dropdowns. As well as Barangays after selecting from the province/city or municipality dropdowns.
		</x-callout>
	</div>
</div>
<div class="row">
	<div class="col-12 col-lg-6">
		<x-form.select wire:model.live='addressForm.regionCode'
			name="addressForm.regionCode"
			label="Region"
			:options="$regions ?? []"
			:selected="$addressForm->regionCode ?? null"
			:required="true" />
	</div>
	<div class="col-12 col-lg-3">
		<x-form.select wire:model.live='addressForm.provinceCode'
			name="addressForm.provinceCode"
			:readonly="$addressForm->regionCode ? false : true"
			label="Province/City"
			:options="$provinces ?? []"
			:selected="$addressForm->provinceCode ?? null" />
	</div>
	<div class="col-12 col-lg-3">
		<x-form.select wire:model.live='addressForm.municipalityCode'
			name="addressForm.municipalityCode"
			:readonly="$addressForm->regionCode ? false : true"
			label="Municipality"
			:options="$municipalities ?? []"
			:selected="$addressForm->provinceCode ?? null" />
	</div>
	<div class="col-12 col-lg-4">
		<x-form.select wire:model.live='addressForm.barangayCode'
			name="addressForm.barangayCode"
			:readonly="$addressForm->regionCode && ($addressForm->provinceCode || $addressForm->municipalityCode) ? false : true"
			label="Barangay"
			:options="$barangays ?? []"
			:selected="$addressForm->barangayCode ?? null"
			:required="true" />
	</div>
	<div class="col-7 col-lg-5">
		<x-form.input wire:model='addressForm.street'
			name="addressForm.street"
			:readonly="$addressForm->barangayCode ? false : true"
			label="Street"
			:required="true" />
	</div>
	<div class="col-5 col-lg-3">
		<x-form.input wire:model='addressForm.houseNumber'
			name="addressForm.houseNumber"
			:readonly="$addressForm->barangayCode ? false : true"
			label="House Number"
			:required="true" />
	</div>
	<div class="col-12"
		wire:loading.delay.longer="addressForm.regionCode, addressForm.provinceCode, addressForm.municipalityCode, addressForm.barangayCode">
		<x-alert>
			<x-slot:icon>
				<span class="spinner-border text-primary"></span>
			</x-slot:icon>
			Loading data. Please wait...
		</x-alert>
	</div>
</div>
