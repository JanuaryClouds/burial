<div class="row">
	{{-- start::Start Date --}}
	<div class="col-12 col-lg-6">
		<div class="row">
			<div class="col-4">
				<x-form.input wire:model.live.blur='startDate'
					name="startDate"
					label="From Date"
					type="date" />
			</div>
			<div class="col-8 d-flex flex-column gap-2">
				<div class="text-muted">Filter by...</div>
				<div class="d-flex gap-2">
					<x-button wire:click="$set('startDate', '{{ date('Y-m-d') }}')"
						class="btn-light">
						<x-icon.font-awesome class="fa-calendar-day" />
						Today
					</x-button>
					<x-button wire:click="$set('startDate', '{{ now()->firstOfYear()->format('Y-m-d') }}')"
						class="btn-light">
						<x-icon.font-awesome class="fa-calendar" />
						Start of this Year
					</x-button>
				</div>
			</div>
		</div>
	</div>
	{{-- end::Start Date --}}

	{{-- start::End Date --}}
	<div class="col-12 col-lg-6">
		<div class="row">
			<div class="col-4">
				<x-form.input wire:model.live.blur='endDate'
					name="endDate"
					label="To Date"
					type="date" />
			</div>
			<div class="col-8 d-flex flex-column gap-2">
				<div class="text-muted">Filter by...</div>
				<div class="d-flex gap-2">
					<x-button wire:click="$set('endDate', '{{ date('Y-m-d') }}')"
						class="btn-light">
						<x-icon.font-awesome class="fa-calendar-day" />
						Today
					</x-button>
					<x-button wire:click="$set('endDate', '{{ now()->endOfYear()->format('Y-m-d') }}')"
						class="btn-light">
						<x-icon.font-awesome class="fa-calendar" />
						End of this Year
					</x-button>
				</div>
			</div>
		</div>
	</div>
	{{-- end::End Date --}}

	{{-- start::Submit --}}
	<div class="col-12 d-flex justify-content-end">
		<x-button wire:click="filter"
			wire:loading.attr='disabled'
			class="btn-sm btn-primary">
			<x-icon.font-awesome class="fa-filter" />
			Filter
		</x-button>
	</div>
	{{-- end::Submit --}}
</div>
