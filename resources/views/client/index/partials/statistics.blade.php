<div class="row">
	@role('staff')
		<div class="col-6 col-lg-4">
			<livewire:client.statistics.total />
		</div>
	@endrole
	@role('staff')
		<div class="col-6 col-lg-4">
			<livewire:client.statistics.current-month />
		</div>
	@endrole
</div>
