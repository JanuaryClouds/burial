@props([
    'type',
    'startDate' => now()->startOfYear()->format('Y-m-d'),
    'endDate' => now()->endOfYear()->format('Y-m-d')
])
<form action="{{ route('superadmin.reports.' . $type) }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Filter Data</h5>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form-input
                        type="date"
                        name="start_date"
                        label="Start Date"
                        id="start_date"
                        value="{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}"
                    />
                </div>
                <div class="col-12 col-lg-6">
                    <x-form-input
                        type="date"
                        name="end_date"
                        label="End Date"
                        id="end_date"
                        value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}"
                    />
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-baseline">
                <p class="text-muted mr-2 mb-0">Presets:</p>
                <button class="btn btn-secondary mr-2" type="button" id="preset-year">This Year</button>
                <button class="btn btn-secondary mr-2" type="button" id="preset-month-prev">Last Month</button>
                <button class="btn btn-secondary mr-2" type="button" id="preset-month-now">This Month</button>
            </span>
            <span class="d-flex align-items-center">
                <a href="{{ route('superadmin.reports.deceased') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-sync"></i>
                    Reset
                </a>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
            </span>
        </div>
    </div>
</form>

<script>
    document.getElementById('preset-year').addEventListener('click', function() {
        document.getElementById('start_date').value = '{{ now()->startOfYear()->format('Y-m-d') }}';
        document.getElementById('end_date').value = '{{ now()->endOfYear()->format('Y-m-d') }}';
        this.form.submit();
    });
    document.getElementById('preset-month-prev').addEventListener('click', function() {
        document.getElementById('start_date').value = '{{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}';
        document.getElementById('end_date').value = '{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}';
        this.form.submit();
    });
    document.getElementById('preset-month-now').addEventListener('click', function() {
        document.getElementById('start_date').value = '{{ now()->startOfMonth()->format('Y-m-d') }}';
        document.getElementById('end_date').value = '{{ now()->endOfMonth()->format('Y-m-d') }}';
        this.form.submit();
    });
</script>