@props([
    'data' => [],
    'columns' => [],
    'classes' => '',
    'resource' => '',
    'route' => '',
])
<div class="table-responsive overflow-x-hidden">
    <div class="dataTables_wrapper">
        <table class="table data-table {{ $classes }}" style="width:100%" data-route="{{ $route ?? '' }}"
            data-columns="{{ json_encode($columns) }}">
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
