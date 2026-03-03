<div class="card">
    <div class="card-header">
        <h2 class="card-title fw-medium fs-2">Latest Clients</h2>
    </div>
    <div class="card-body">
        @if (!isset($data) || $data->isEmpty())
            <p class="text-muted text-center">No Data</p>
        @else
            @include('partials.datatable.index', [
                'data' => $data,
                'resource' => $resource,
                'columns' => $columns,
                'route' => route('client.index'),
                'classes' => 'with-status',
            ])
        @endif
    </div>
</div>
