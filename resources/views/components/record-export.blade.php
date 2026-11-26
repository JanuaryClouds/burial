@props(['report'])
@can('create-reports')
<div>
    @if(session('export_message'))
        <div role="status" class="alert alert-info mt-3">{{ session('export_message') }}</div>
    @endif
    @if($errors->any())
        <div role="alert" class="alert alert-danger mt-3">{{ $errors->first() }}</div>
    @endif
    <div class="modal fade" id="record-export" tabindex="-1" aria-labelledby="export-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
            <form method="GET" action="{{ route($report.'.export.all') }}" data-record-export data-no-loader>
                <div class="modal-header"><h2 id="export-title" class="modal-title">Export {{ ucfirst($report) }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body d-grid gap-3">
                    <div data-export-error class="alert alert-warning" role="alert" hidden></div>
                    <div class="row g-3">
                    @foreach(['from' => 'from', 'until' => 'through'] as $field => $label)
                        <label class="col-sm-6">{{ $report === 'applications' ? 'Submitted' : 'Recorded' }} {{ $label }}
                            <input type="date" name="{{ $field }}" value="{{ old($field) }}" class="form-control"></label>
                    @endforeach
                    </div>
                    <label>Application status<select name="status" class="form-select"><option value="">All</option>
                        @foreach(\App\Services\RecordExcelExport::STATUSES as $status)
                            <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select></label>
                    <label>{{ $report === 'applications' ? 'Current claimant barangay' : 'Barangay' }}
                        <input class="form-control" name="barangay" placeholder="All" maxlength="150" value="{{ old('barangay') }}"></label>
                    @if($report === 'applications')
                        <label>Assistance type<select class="form-select" name="assistance"><option value="">All</option>
                        @foreach(\App\Models\FuneralAssistanceType::withTrashed()->orderBy('name')->get(['uuid','name']) as $type)
                            <option value="{{ $type->uuid }}" @selected(old('assistance') === $type->uuid)>{{ $type->name }}</option>
                        @endforeach
                        </select></label>
                        <label><input type="checkbox" name="workflow_history" value="1" @checked(old('workflow_history'))> Include workflow history</label>
                        <label><input type="checkbox" name="claimant_changes" value="1" @checked(old('claimant_changes'))> Include claimant changes</label>
                    @elseif($report === 'beneficiaries')
                        <label>Sex<select class="form-select" name="sex"><option value="">All</option>
                        @foreach(\App\Models\Sex::orderBy('name')->get() as $sex)
                            <option value="{{ $sex->id }}" @selected(old('sex') == $sex->id)>{{ $sex->name }}</option>
                        @endforeach
                        </select></label>
                        <label>PWD<select class="form-select" name="pwd"><option value="">All</option><option value="1" @selected(old('pwd') === '1')>Yes</option><option value="0" @selected(old('pwd') === '0')>No</option></select></label>
                    @endif
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Download Excel</button></div>
            </form>
        </div></div>
    </div>
</div>

@endcan
