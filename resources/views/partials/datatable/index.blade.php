@props([
    'data' => collect([]),
    'columns' => [],
    'classes' => '',
    'route' => null,
])
@php
    if ($data->isNotEmpty()) {
        $firstItem = $data->first();

        $firstItemArray = is_array($firstItem) ? $firstItem : $firstItem->toArray();
        if (array_key_exists('show_route', $firstItemArray)) {
            $classes .= ' with-actions';
        }

        if (array_key_exists('status', $firstItemArray)) {
            $classes .= ' with-status';
        }
    }
@endphp
@if ($data->count() > 0)
    <div class="table-responsive overflow-x-hidden">
        <div class="dataTables_wrapper">
            <table class="table data-table {{ $classes }}" style="width:100%"
                data-route="{{ $route ?? request()->url() }}" data-columns='@json($columns)'>
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
@else
    <p class="text-muted text-center mb-0">No Data</p>
@endif
