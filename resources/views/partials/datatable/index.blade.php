@props([
    'data' => collect([]),
    'columns' => [],
    'classes' => '',
    'route' => null,
    'src' => null,
    'countPerPage' => 10,
])
@php
    if ($data->isNotEmpty()) {
        $hasActions = $data->contains(
            fn($item) => array_key_exists('show_route', $item) && $item['show_route'] !== null,
        );
        $hasStatus = $data->contains(fn($item) => array_key_exists('status', $item));

        if ($hasActions) {
            $classes .= ' with-actions';
        }

        if ($hasStatus) {
            $classes .= ' with-status';
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
    <div class="dataTables_wrapper">
        <table class="table data-table {{ $classes }}" id="dataTable-{{ $dataTableId }}" style="width:100%"
            data-route="{{ $route ?? request()->url() }}" data-columns='@json($columns)'
            data-rows='@json($data)' data-src="{{ $src }}"
            data-count-per-page="{{ $countPerPage }}">
            <thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
                @include('partials.datatable.head', [
                    'columns' => $columns,
                ])
            </thead>
            <tbody>
                @include('partials.datatable.body', [
                    'columns' => $columns,
                ])
            </tbody>
        </table>
    </div>
</div>
@include('partials.datatable.script')
