@props([
    'data' => collect([]),
    'columns' => [],
    'classes' => '',
    'route' => null,
    'src' => null,
    'countPerPage' => 10,
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

	$dataTableId = (string) Str::uuid();

	if ($src === null && $route && $route !== '#' && $route !== '' && $data->isNotEmpty()) {
	    throw new RuntimeException(
	        'Src is required when data is present and route is present. Please provide a src or route.',
	    );
	}
@endphp
<div class="table-responsive overflow-x-hidden">
	<table class="table data-table {{ $classes }}"
		id="dataTable-{{ $dataTableId }}"
		style="width:100%"
		data-route="{{ $route ?? request()->url() }}"
		data-columns='@json($columns)'
		data-rows='@json($data)'
		data-src="{{ $src }}"
		data-count-per-page="{{ $countPerPage }}">
		<thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
			@include('partials.datatable.head', [
				'columns' => $columns,
			])
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
@include('partials.datatable.script')
