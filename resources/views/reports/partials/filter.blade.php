@props(['type', 'startDate' => now()->startOfYear(), 'endDate' => now()->endOfYear(), 'uid' => \Illuminate\Support\Str::uuid()])
<form action="{{ route('reports.' . $type) }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Filter Data</h5>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form-input type="datetime-local" name="start_date" label="Start Date"
                        id="start_date_{{ $uid }}"
                        value="{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d\TH:i') }}" />
                </div>
                <div class="col-12 col-lg-6">
                    <x-form-input type="datetime-local" name="end_date" label="End Date"
                        id="end_date_{{ $uid }}"
                        value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d\TH:i') }}" />
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="d-flex align-items-baseline gap-3">
                <p class="text-muted mr-2 mb-0">Presets:</p>
                <button class="btn btn-secondary mr-2" type="button" id="preset-year-{{ $uid }}">This
                    Year</button>
                <button class="btn btn-secondary mr-2" type="button" id="preset-month-prev-{{ $uid }}">Last
                    Month</button>
                <button class="btn btn-secondary mr-2" type="button" id="preset-month-now-{{ $uid }}">This
                    Month</button>
            </span>
            <span class="d-flex align-items-center gap-3">
                <a href="{{ route('reports.' . $type) }}" class="btn btn-secondary mr-2">
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

<script nonce="{{ $nonce ?? '' }}">
    const uid = '{{ $uid }}';

    document.getElementById('preset-year-' + uid).addEventListener('click', function() {
        document.getElementById('start_date_' + uid).value =
            '{{ now()->startOfYear()->format('Y-m-d\TH:i') }}';
        document.getElementById('end_date_' + uid).value = '{{ now()->endOfYear()->format('Y-m-d\TH:i') }}';
        this.form.submit();
    });
    document.getElementById('preset-month-prev-' + uid).addEventListener('click', function() {
        document.getElementById('start_date_' + uid).value =
            '{{ now()->subMonthNoOverflow()->startOfMonth()->format('Y-m-d\TH:i') }}';
        document.getElementById('end_date_' + uid).value =
            '{{ now()->startOfMonth()->subSeconds(1)->format('Y-m-d\TH:i') }}';
        this.form.submit();
    });
    document.getElementById('preset-month-now-' + uid).addEventListener('click', function() {
        document.getElementById('start_date_' + uid).value =
            '{{ now()->startOfMonth()->format('Y-m-d\TH:i') }}';
        document.getElementById('end_date_' + uid).value = '{{ now()->endOfMonth()->format('Y-m-d\TH:i') }}';
        this.form.submit();
    });
</script>
