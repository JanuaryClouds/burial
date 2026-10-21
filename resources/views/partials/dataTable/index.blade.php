@props([
    'data' => collect([]),
    'columns' => [],
    'classes' => '',
    'route' => null,
    'src' => null,
    'countPerPage' => 10,
    'dataTableId',
])
@php
	if ($columns->isNotEmpty()) {
	    $hasActions = in_array('show_route', $columns->pluck('data')->toArray());
	    $hasStatus = in_array('status', $columns->pluck('data')->toArray());
	    if ($hasStatus) {
	        $classes .= ' with-status';
	    }

	    if ($hasActions) {
	        $classes .= ' with-actions';
	    }
	}

	if ($src === null && $route && $route !== '#' && $route !== '' && $data->isNotEmpty()) {
	    throw new RuntimeException(
	        'Src is required when data is present and route is present. Please provide a src or route.',
	    );
	}
@endphp
<div class="table-responsive overflow-x-hidden"
	wire:ignore>
	<table class="table data-table {{ $classes }}"
		id="dataTable-{{ $dataTableId }}"
		style="width:100%"
		data-route="{{ $route ?? ($src ? request()->url() : null) }}"
		data-columns='@json($columns)'
		data-rows='@json($data)'
		data-src="{{ $src }}"
		data-count-per-page="{{ $countPerPage }}">
		<thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
			@include('partials.dataTable.head', [
				'columns' => $columns,
			])
		</thead>
		<tbody>
			@include('partials.dataTable.body', [
				'columns' => $columns,
				'rows' => $data,
			])
		</tbody>
	</table>
</div>
