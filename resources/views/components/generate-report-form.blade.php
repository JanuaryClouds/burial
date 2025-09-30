@props(['type'])
<form action="{{ route('superadmin.reports.generate') }}" method="post">
    @csrf
    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#generate-report">
        <i class="fas fa-file-excel"></i>
        Export to Excel
    </button>
    <div id="generate-report" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <x-form-input
                        type="text"
                        name="report_type"
                        value="{{ $type }}"
                        readonly="true"
                        label="Export"
                    />
                    <x-form-input
                        type="date"
                        name="start_date"
                        label="Start Date"
                    />
                    <x-form-input
                        type="date"
                        name="end_date"
                        label="End Date"
                    />
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-check"></i>
                        Generate
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>