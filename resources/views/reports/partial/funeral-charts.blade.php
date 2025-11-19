<div class="col-12 col-lg-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Funeral Assistance Per Status</h4>
        </div>
        <div class="card-body">
            @if (!$funeralsPerStatus->isEmpty())
                <canvas 
                    id="funerals-per-status"
                    data-chart-data='@json($funeralsPerStatus->pluck('count'))'
                    data-chart-labels='@json($funeralsPerStatus->pluck('name'))'
                    data-chart-type="bar"
                    data-empty="{{ $funeralsPerStatus->isEmpty() ? 'true' : 'false' }}"
                ></canvas>
            @else
                <p class="text-muted">No Data</p>
            @endif
        </div>
    </div>
</div>