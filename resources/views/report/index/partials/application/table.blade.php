<div>
	<div class="row">
		<div class="col-12">
			<x-card>
				<x-slot:header>Applications</x-slot:header>
				@include('partials.dataTable.index', [
					'data' => $applications,
					'columns' => $columns,
					'dataTableId' => 'applications-table',
				])
			</x-card>
		</div>
	</div>
</div>
