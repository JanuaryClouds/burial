<div class="row">
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Beneficiaries Per Age Group</x-slot:header>
			<x-chart chartId="beneficiaries-per-age-group"
				:chartData="$beneficiariesPerAgeGroup"
				chartType="pie" />
		</x-card>
	</div>
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Beneficiaries Per Religion</x-slot:header>
			<x-chart chartId="beneficiaries-per-religion"
				:chartData="$beneficiariesPerReligion"
				chartType="pie" />
		</x-card>
	</div>
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Perinatal and Neonatal Deaths</x-slot:header>
			<x-chart chartId="beneficiaries-per-natality"
				:chartData="$beneficiariesPerNatality"
				chartType="pie" />
		</x-card>
	</div>
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Beneficiaries per Month</x-slot:header>
			<x-chart chartId="beneficiaries-per-month"
				:chartData="$beneficiariesPerMonth"
				chartType="line" />
		</x-card>
	</div>
</div>
