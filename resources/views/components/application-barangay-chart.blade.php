<div class="col-12 col-md-12 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4>Claimants By Barangay</h4>
        </div>
        <div class="card-body">
            <div>
                <canvas
                    id="application-by-barangay"
                    data-chart-data='@json($applications->pluck('count'))'
                    data-chart-labels='@json($applications->pluck('barangay'))'
                    data-chart-type="bar"
                    data-empty="{{ $applications->isEmpty() ? 'true' : 'false' }}"
                ></canvas>
            </div>
        </div>
    </div>
</div>