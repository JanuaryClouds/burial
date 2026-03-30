@props([
    'data' => null,
    'columns' => [],
    'route' => $route ?? route('client.index'),
])
<div class="card">
    <div class="card-header">
        <h2 class="card-title fw-medium fs-2">Latest Clients</h2>
    </div>
    <div class="card-body">
        @if (!$data || $data->isEmpty())
            <p class="text-muted text-center">No Data</p>
        @else
            @include('partials.datatable.index', [
                'data' => $data,
                'columns' => $columns,
                'route' => $route ?? route('client.index'),
            ])
        @endif
    </div>
</div>
