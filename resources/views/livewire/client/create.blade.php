<x-slot:page_title>Create Client</x-slot:page_title>
<x-slot:page_subtitle>Funeral Assistance System | CSWDO Taguig</x-slot:page_subtitle>
<div class="d-flex flex-column gap-4">
	<x-card>
		{{-- Basic Information --}}
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
			<div class="col-12 col-md-4 col-lg-3 col-xl-2">
				<x-form.input wire:model='dateOfBirth'
					name="dateOfBirth"
					label="Date of Birth"
					:required="true"
					max="{{ now() }}"
					type="date" />
			</div>
			<div class="col-6 col-md-4 col-lg-2 col-xl-2">
				<x-form.select wire:model='sexId'
					name="sexId"
					label="Sex"
					:options="$genders ?? []"
					:required="true" />
			</div>
			<div class="col-6 col-md-4 col-lg-3 col-xl-2">
				<x-form.select wire:model='civilId'
					name="civilId"
					label="Civil Status"
					:options="$civilStatus ?? []"
					:required="true" />
			</div>
			<div class="col-12 col-md-4 col-lg-4 col-xl-2">
				<x-form.select wire:model='nationalityId'
					name="nationalityId"
					label="Nationality"
					:options="$nationalities ?? []"
					:required="true" />
			</div>
			<div class="col-12 col-lg-5 col-xl-4">
				<x-form.select wire:model='religionId'
					name="religionId"
					label="Religion"
					:options="$religions ?? []"
					:required="true" />
			</div>
		</div>

		<div class="separator separator-dashed my-4"></div>

		{{-- Address --}}
		<div class="row">
			<div class="col-5 col-md-3 col-lg-3 col-xl-2">
				<x-form.input wire:model='houseNo'
					name="houseNo"
					label="House Number"
					:required="true" />
			</div>
			<div class="col-7 col-md-4 col-lg-9 col-xl-3">
				<x-form.input wire:model='street'
					name="street"
					label="Street"
					:required="true" />
			</div>
			<div class="col-8 col-md-3 col-lg-4 col-xl-2">
				<x-form.select wire:model='barangayId'
					name="barangayId"
					label="Barangay"
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

		<div class="separator separator-dashed my-4"></div>

		{{-- Social Information --}}
		<div class="row">
			<div class="col-6 col-lg-6 col-xl-3">
				<x-form.input wire:model='contactNumber'
					name="contactNumber"
					label="Contact Number"
					type="text"
					:required="true" />
			</div>
			<div class="col-6 col-lg-6 col-xl-3">
				<x-form.select wire:model='educationId'
					name="educationId"
					label="Educational Attainment"
					:options="$educations ?? []" />
			</div>
			<div class="col-12 col-lg-6 col-xl-3">
				<x-form.input wire:model='philhealth'
					name="philhealth"
					label="PhilHealth ID" />
			</div>
			<div class="col-12 col-md-6 col-lg-6 col-xl-3">
				<x-form.input wire:model='skill'
					name="skills"
					label="Skills/Occupation" />
			</div>
			<div class="col-12 col-md-6 col-lg-6 col-xl-3">
				<x-form.input wire:model='income'
					name="income"
					label="Estimated Monthly Income" />
			</div>
		</div>
		<x-slot:footer>
			<x-button class="btn-sm btn-success"
				wire:click="save"
				wire:loading.attr="disabled">
				<x-icon.font-awesome class="fa-floppy-disk" />
				<span wire:loading.remove>
					Save Draft
				</span>
				<span wire:loading>
					Saving Draft...
				</span>
			</x-button>
		</x-slot:footer>
	</x-card>
</div>
