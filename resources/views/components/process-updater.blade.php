@php
    $nextStep = null;

    if ($processLogs->count() != 0) {
        $processLogs = $processLogs->sortBy(fn($log) => $log->created_at)->values();
        $lastStep = $processLogs->last()?->loggable;
        $nextStep = $lastStep?->order_no ?? 1;

        $claimantChangeIndex = $processLogs
            ->keys()
            ->first(fn($key) => class_basename($processLogs[$key]->loggable) === 'ClaimantChange');
        $schema = $lastStep?->extra_data_schema ? json_decode($lastStep->extra_data_schema, true) : [];
        if (class_basename($lastStep) === 'ClaimantChange') {
            $claimantChangeLog = $processLogs[$claimantChangeIndex];
            $status = $claimantChangeLog->loggable->status ?? null;
            if ($status === 'approved') {
                $nextOrderNo = 2; // Reset to 2 on approval
            } elseif ($status === 'rejected') {
                $previousLog = $processLogs[$claimantChangeIndex - 1] ?? null;
                $nextOrderNo = $previousLog?->loggable?->order_no ?? 1;
            }
        } else {
            $nextOrderNo = $lastStep?->order_no ?? 1;
        }
    }
@endphp
<div id="addUpdateModal-{{ $application->id }}" class="modal fade flex justify-content-center" tabindex="-1" role="dialog" aria-labelledby="add-process" aria-hidden="true">
    @foreach ($workflowSteps as $step)
        @if ($processLogs->count() == 0 || ($step?->order_no > $nextOrderNo))
            <div class="modal-dialog" role="document">
                <form action="{{ route('admin.application.addLog', ['id' => $application->id, 'stepId' => $step->id]) }}" method="post" id="addLogForm">
                @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="add-proces">Add Process Update</h5>
                            <button class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <section class="section">
                                <div class="section-title">
                                    <h6 class="text-muted">Previous Step: {{ $processLogs->last()->loggable?->description ?? 'Submitted at ' . $application->application_date }}</h6>
                                    <div class="row mt-4">
                                        <div class="form-group col-lg-6 col-sm-12 mb-0">
                                            <p class="text-muted">Date Out: {{ $processLogs->last()?->date_out }}</p>
                                        </div>
                                        <div class="form-group col-lg-6 col-sm-12 mb-0">
                                            <p class="text-muted">Date In: {{ $processLogs->last()?->date_in }}</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section class="section">
                                <div class="section-title">
                                    <h6>Next Step: {{ $step->description }}</h6>
                                    <div class="row mt-4">
                                        <div class="form-group col-lg-6 col-sm-12 mb-0">
                                            <x-form-input type="date" name="date_out" id="date_out" label="Date Out" min="{{ Carbon\Carbon::parse($processLogs->last()?->date_in)->format('Y-m-d') }}" />
                                        </div>
                                        <div class="form-group col-lg-6 col-sm-12 mb-0">
                                            <x-form-input type="date" name="date_in" id="date_in" label="Date In" :required="true" min="{{ Carbon\Carbon::parse($processLogs->last()?->date_out)->format('Y-m-d') }}"/>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $schema = $step->extra_data_schema ? json_decode($step->extra_data_schema, true) : [];
                                @endphp
                                @foreach ($schema as $key => $field)
                                    @if(is_string($field))
                                        <div class="form-group col-12 p-0">
                                            <label for="extra_data[{{ str_replace('*', '', $key) }}]" >
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                                            </label>
                                            <input 
                                                type="{{ $field }}"
                                                class="form-control"
                                                name="extra_data[{{ str_replace('*', '', $key) }}]"
                                                {{ str_contains($field, '*') ? 'required' : '' }}
                                            >
                                        </div>
                                    @elseif (is_array($field))
                                        @foreach ($field as $subkey => $subField)
                                            <div class="form-group col-12 p-0">
                                                <label for="extra_data[{{ $key }}][{{ str_replace('*', '', $subkey) }}]" >
                                                    {{ ucfirst(str_replace('_', ' ', $subkey)) }}
                                                </label>
                                                <input 
                                                    type="{{ $subField }}" 
                                                    class="form-control" 
                                                    name="extra_data[{{ $key }}][{{ str_replace('*', '', $subkey) }}]" 
                                                    {{ str_contains($subField, '*') ? 'required' : '' }}
                                                >
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                                <div class="form-group">
                                    <label for="comments">Comments</label>
                                    <textarea id="comments" class="form-control" name="comments" rows="3"></textarea>
                                </div>
                            </section>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-check"></i>
                                Submit
                            </button>
                            <button class="btn btn-secondary" type="button" data-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @break
        @endif
    @endforeach
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dateIn = document.getElementById('date_in');
        const dateOut = document.getElementById('date_out');

        if (dateOut && !dateOut.dataset.listenerAttached) {
            dateOut.dataset.listenerAttached = 'true';

            dateOut.addEventListener('change', () => {
                if (dateOut.value) {
                    dateIn.min = dateOut.value;
                }

                if (dateIn.value && dateIn.value < dateOut.value) {
                    dateIn.value = '';
                }
            });
        }
    });
</script>
