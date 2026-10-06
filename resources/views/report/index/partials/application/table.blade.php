<div>
	<div class="row">
		<div class="col-12">
			<x-card>
				<x-slot:header>Applications</x-slot:header>
				@include('partials.datatable.index', [
					'data' => $applications,
					'columns' => $columns,
					'route' => '#',
				])
			</x-card>
		</div>
	</div>
</div>
