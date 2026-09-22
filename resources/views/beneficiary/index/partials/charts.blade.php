<div class="row">
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Beneficiaries Per Age Group</x-slot:header>
			<livewire:beneficiary.charts.per-age-group />
		</x-card>
	</div>
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Beneficiaries Per Religion</x-slot:header>
			<livewire:beneficiary.charts.per-religion />
		</x-card>
	</div>
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Perinatal and Neonatal Deaths</x-slot:header>
			<livewire:beneficiary.charts.per-natality />
		</x-card>
	</div>
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Beneficiaries per Month</x-slot:header>
			<livewire:beneficiary.charts.per-month />
		</x-card>
	</div>
</div>
