<div class="row">
	@role('staff')
		<div class="col-6 col-lg-4">
			<x-counter :count="$totalClientsCount"
				iconName="people"
				:iconPathsCount="5"
				label="Total Number of Clients" />
		</div>
	@endrole
	@role('staff')
		<div class="col-6 col-lg-4">
			<x-counter :count="$currentMonthClientsCount"
				iconName="calendar"
				:iconPathsCount="2"
				label="Current Month Clients" />
		</div>
	@endrole
</div>
