<div class="row gap-2">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Deceased By Gender</h4>
            </div>
            <div class="card-body">
                @if (!$deceasedPerGender->isEmpty())
                    <canvas 
                        id="deceased-per-gender"
                        data-chart-data='@json($deceasedPerGender->pluck('count'))'
                        data-chart-labels='@json($deceasedPerGender->pluck('name'))'
                        data-chart-type="bar"
                        data-empty="{{ $deceasedPerGender->isEmpty() ? 'true' : 'false' }}"
                    ></canvas>
                @else
                    <p class="text-muted">No Data</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Deceased Per Religion</h4>
            </div>
            <div class="card-body">
                @if (!$deceasedPerReligion->isEmpty())
                    <canvas
                        id="deceased-per-religion"
                        data-chart-data='@json($deceasedPerReligion->pluck('count'))'
                        data-chart-labels='@json($deceasedPerReligion->pluck('name'))'
                        data-chart-type="pie"
                        data-empty="{{ $deceasedPerReligion->isEmpty() ? 'true' : 'false' }}"
                    ></canvas>
                @else
                    <p class="text-muted">No Data</p>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row gap-2 mt-8">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Applied Deceased per Period</h4>
            </div>
            <div class="card-body">
                @if (!$deceasedThisMonth->isEmpty())
                    <canvas 
                        id="deceased-per-month"
                        data-chart-data='@json($deceasedThisMonth->pluck('count'))'
                        data-chart-labels='@json($deceasedThisMonth->pluck('period'))'
                        data-chart-type="bar"
                        data-empty="{{ $deceasedThisMonth->isEmpty() ? 'true' : 'false' }}"
                    ></canvas>
                @else
                    <p class="text-muted">No Data</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Deceased Per Barangay</h4>
            </div>
            <div class="card-body">
                @if (!$deceasedPerBarangay->isEmpty())
                    <canvas
                        id="deceased-per-barangay"
                        data-chart-data='@json($deceasedPerBarangay->pluck('count'))'
                        data-chart-labels='@json($deceasedPerBarangay->pluck('name'))'
                        data-chart-type="pie"
                        data-empty="{{ $deceasedPerBarangay->isEmpty() ? 'true' : 'false' }}"
                    ></canvas>
                @else
                    <p class="text-muted">No Data</p>
                @endif
            </div>
        </div>
    </div>
</div>