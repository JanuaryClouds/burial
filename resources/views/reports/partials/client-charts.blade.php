<div class="col-12 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Clients Per Barangay</h4>
        </div>
        <div class="card-body">
            @if (!$clientsPerBarangay->isEmpty())
                <canvas 
                    id="clients-per-barangay"
                data-chart-data='@json($clientsPerBarangay->pluck('count'))'
                    data-chart-labels='@json($clientsPerBarangay->pluck('name'))'
                    data-chart-type="bar"
                    data-empty="{{ $clientsPerBarangay->isEmpty() ? 'true' : 'false' }}"
                ></canvas>
            @else
                <p class="text-muted">No Data</p>
            @endif
        </div>
    </div>
</div>
<div class="col-12 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Clients Per Assistance</h4>
        </div>
        <div class="card-body">
            @if (!$clientsPerAssistance->isEmpty())
                <canvas 
                    id="clients-per-assistance"
                data-chart-data='@json($clientsPerAssistance->pluck('count'))'
                    data-chart-labels='@json($clientsPerAssistance->pluck('name'))'
                    data-chart-type="bar"
                    data-empty="{{ $clientsPerAssistance->isEmpty() ? 'true' : 'false' }}"
                ></canvas>
            @else
                <p class="text-muted">No Data</p>
            @endif
        </div>
    </div>
</div>