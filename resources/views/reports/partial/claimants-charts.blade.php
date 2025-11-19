<div class="col-12 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Claimants of Burial Assistance per Barangay</h4>
        </div>
        <div class="card-body">
            <canvas 
                id="claimant-per-barangay"
                data-chart-data='@json($claimantsPerBarangay->pluck('count'))'
                data-chart-labels='@json($claimantsPerBarangay->pluck('name'))'
                data-chart-type="pie"
            ></canvas>
        </div>
    </div>
</div>
<div class="col-12 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Relationships to Deceased</h4>
        </div>
        <div class="card-body">
            <canvas 
                id="claimant-per-relationship"
                data-chart-data='@json($claimantsPerRelationship->pluck('count'))'
                data-chart-labels='@json($claimantsPerRelationship->pluck('name'))'
                data-chart-type="pie"
            ></canvas>
        </div>
    </div>
</div>