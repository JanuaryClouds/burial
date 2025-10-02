@props(['type'])
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
                        value="{{ now()->subMonth()->format('Y-m-d') }}"
                    />
                </div>
                <div class="col-12 col-lg-6">
                    <x-form-input
                        type="date"
                        name="end_date"
                        label="End Date"
                        value="{{ now()->addMonth()->format('Y-m-d') }}"
                    />
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('superadmin.reports.deceased') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-sync"></i>
                Reset
            </a>
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-filter"></i>
                Filter
            </button>
        </div>
    </div>
</form>