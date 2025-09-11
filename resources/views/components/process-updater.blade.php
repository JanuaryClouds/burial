@php
    $lastStep = $processLogs->last()?->workflowStep;
    $schema = $lastStep?->extra_data_schema ? json_decode($lastStep->extra_data_schema, true) : [];
@endphp

<div id="add-process" class="modal fade flex justify-content-center" tabindex="-1" role="dialog" aria-labelledby="add-proces" aria-hidden="true">
    @foreach ($workflowSteps as $step)
        @if ($processLogs->count() == 0 || ($step->order_no > $processLogs->last()->workflowStep->order_no))
            <div class="modal-dialog" role="document">
                <form action="{{ route('admin.application.addLog', ['status' => $application->status, 'id' => $application->id, 'step' => $step->id]) }}" method="post">
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
                                    <h6 class="text-muted">Previous Step: {{ $processLogs->last()->workflowStep->description ?? 'Submitted at ' . $application->application_date }}</h6>
                                </div>
                            </section>
                            <section class="section">
                                <div class="section-title">
                                    <h6>Next Step: {{ $step->description }}</h6>
                                        <div class="row">
                                            <div class="form-group col-lg-6 col-sm-12">
                                                <x-form-input type="date" name="date_out" id="date_out" label="Date Out" />
                                            </div>
                                            <div class="form-group col-lg-6 col-sm-12">
                                                <x-form-input type="date" name="date_in" id="date_in" label="Date In" :required="true"/>
                                            </div>
                                        </div>
                                        @foreach ($schema as $key => $field)
                                            @if(is_string($field))
                                                <div class="form-group col-lg-6 col-sm-12">
                                                    <label for="">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                                    <input 
                                                        type="{{ $field }}"
                                                        class="form-control"
                                                        name="extra[{{ $key }}]"
                                                        required
                                                    >
                                                </div>
                                            @elseif (is_array($field))
                                                @foreach ($field as $subkey => $subField)
                                                    <div class="form-group col-lg-6 col-sm-12">
                                                        <label for="">{{ ucfirst(str_replace('_', ' ', $subkey)) }}</label>
                                                        <input type="{{ $subField }}" class="form-control" name="extra[{{ $key }}][{{ $subkey }} }}" required>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endforeach
                                        <div class="form-group">
                                            <label for="comments">Comments</label>
                                            <textarea id="comments" class="form-control" name="comments" rows="3"></textarea>
                                        </div>
                                </div>
                            </section>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
            @break
        @endif
    @endforeach
</div>