<div class="col-12 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Deceased Per Religion</h4>
        </div>
        <div class="card-body">
            <div>
                <canvas
                    id="deceased-per-religion"
                    data-chart-data='@json($deceasedPerReligion->pluck('count'))'
                    data-chart-labels='@json($deceasedPerReligion->pluck('name'))'
                    data-chart-type="bar"
                    data-empty="{{ $deceasedPerReligion->isEmpty() ? 'true' : 'false' }}"
                ></canvas>
            </div>
        </div>
    </div>
</div>
<div class="col-12 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Deceased Per Barangay</h4>
        </div>
        <div class="card-body">
            <div>
                <canvas
                    id="deceased-per-barangay"
                    data-chart-data='@json($deceasedPerBarangay->pluck('count'))'
                    data-chart-labels='@json($deceasedPerBarangay->pluck('name'))'
                    data-chart-type="bar"
                    data-empty="{{ $deceasedPerBarangay->isEmpty() ? 'true' : 'false' }}"
                ></canvas>
            </div>
        </div>
    </div>
</div>