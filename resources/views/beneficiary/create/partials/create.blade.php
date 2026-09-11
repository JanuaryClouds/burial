<x-card>
	<x-slot:header>Beneficiary's Information</x-slot:header>
	<div class="d-flex flex-column gap-4">
		<div class="row">
			<div class="col-12 col-md-6">
				<x-callout class="bg-danger-subtle border-danger-subtle h-100">
					<x-slot:icon>
						<x-icon.font-awesome class="fa-triangle-exclamation text-danger fs-2" />
					</x-slot:icon>
					<x-slot:title>
						<p class="text-danger fs-6 fw-bold">This from has required fields</p>
					</x-slot:title>
					<p class="text-danger">All fields marked with an asterisk (*) are required to be filled out.</p>
				</x-callout>
			</div>
			<div class="col-12 col-md-6 d-flex flex-grow">
				<x-callout class="bg-info-subtle border-info-subtle h-100">
					<x-slot:icon>
						<x-icon.font-awesome class="fa-info-circle text-info fs-2" />
					</x-slot:icon>
					<p class="text-info">After submission, you will be redirected to filling out the beneficiary's information. This
						will be saved as a draft, allowing you to return and complete it at a later time.</p>
				</x-callout>
			</div>
		</div>

		{{-- Basic Information --}}
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
		</div>

		<div class="row">
			<div class="col-12 col-md-4 col-lg-2 col-xl-2">
				<x-form.select wire:model='sexId'
					name="sexId"
					label="Sex"
					:selected="$sexId ?? ''"
					:options="$genders ?? []"
					:required="true" />
			</div>
			<div class="col-12 col-md-8 col-lg-5 col-xl-4">
				<x-form.select wire:model='religionId'
					name="religionId"
					label="Religion"
					:selected="$religionId ?? ''"
					:options="$religions ?? []"
					:required="true" />
			</div>
		</div>

		<div class="separator separator-dashed my-4"></div>

		<div class="row">
			<div class="col-6 col-md-6 col-lg-3 col-xl-3">
				<x-form.input wire:model.live.blur='dateOfBirth'
					name="dateOfBirth"
					label="Date of Birth"
					:required="true"
					:max="now()"
					type="date" />
			</div>
			<div class="col-6 col-md-6 col-lg-3 col-xl-3">
				<x-form.input wire:model.live.blur='dateOfDeath'
					name="dateOfDeath"
					label="Date of Death"
					:required="true"
					:max="now()"
					type="date" />
			</div>
			<div class="col-12 col-md-12 col-lg-6 col-xl-4 d-flex gap-4">
				@if ($dateOfBirth && $dateOfDeath && \Carbon\Carbon::parse($dateOfDeath)->diffinMonths($dateOfBirth) < 1)
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

		<div class="separator separator-dashed my-4"></div>

		{{-- Address --}}
		<h4>Place of Birth</h4>
		<div class="row">
			<div class="col-12 col-md-6 col-lg-4 col-xl-3">
				<x-form.input wire:model='houseNo'
					name="houseNo"
					label="House No."
					:required="true" />
			</div>
			<div class="col-12 col-md-6 col-lg-8 col-xl-5">
				<x-form.input wire:model='street'
					name="street"
					label="Street"
					:required="true" />
			</div>
			<div class="col-12 col-md-6 col-lg-6 col-xl-2">
				<x-form.select wire:model='barangayId'
					name="barangayId"
					label="Barangay"
					:required="true"
					:selected="$barangayId ?? ''"
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
	</div>
</x-card>
