<div class="col-12 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4>Cheques Per Status</h4>
        </div>
        <div class="card-body">
            @if (!$chequesPerStatus->isEmpty())
                <canvas 
                    id="cheques-per-status"
                    data-chart-data='@json($chequesPerStatus->pluck('count'))'
                    data-chart-labels='@json($chequesPerStatus->pluck('name'))'
                    data-chart-type="bar"
                    data-empty="{{ $chequesPerStatus->isEmpty() ? 'true' : 'false' }}"
                ></canvas>
            @else
                <p class="text-muted">No Data</p>
            @endif
        </div>
    </div>
</div>