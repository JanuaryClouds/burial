<div class="modal fade" id="addUpdateModal-{{ $data->id }}" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" role="dialog" aria-labelledby="addUpdateTo-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('process-logs.store', ['id' => $data->id, 'stepId' => $next_step]) }}" method="post"
                id="addLogForm-{{ $data->id }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addUpdateTo-{{ $data->id }}">
                        Add an Update
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-4">
                    <h6 class="text-muted">
                        Previous Step:
                        {{ last($timeline)['description'] ?? 'Submitted at ' . $data->application_date }}
                    </h6>
                    @if (isset(last($timeline)['description']))
                        <div class="row mb-4">
                            @if (isset(last($timeline)['out']))
                                <div class="col-lg-6 col-sm-12 mb-0">
                                    <p class="text-muted">Date Out: {{ last($timeline)['out'] }}</p>
                                </div>
                            @endif
                            @if (isset(last($timeline)['in']))
                                <div class="col-lg-6 col-sm-12 mb-0">
                                    <p class="text-muted">Date In: {{ last($timeline)['in'] }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                    <h6>Next Step: {{ $next_step->description }}</h6>
                    <div class="row">
                        <x-form-input type="date" name="date_out" id="date_out-{{ $data->id }}" label="Date Out"
                            min="{{ isset(last($timeline)['in']) ? Carbon\Carbon::parse(last($timeline)['in'])->format('Y-m-d') : Carbon\Carbon::parse($data->application_date)->format('Y-m-d') }}" />
                        <x-form-input type="date" name="date_in" id="date_in-{{ $data->id }}" label="Date In"
                            min="{{ isset(last($timeline)['out']) ? Carbon\Carbon::parse(last($timeline)['out'])->format('Y-m-d') : Carbon\Carbon::parse($data->application_date)->format('Y-m-d') }}" />
                    </div>
                    <div class="d-flex flex-column gap-2">
                        @php
                            $schema = $next_step->extra_data_schema
                                ? json_decode($next_step->extra_data_schema, true)
                                : [];
                        @endphp
                        @foreach ($schema as $key => $field)
                            @if (is_string($field))
                                <div class="form-group col-12 p-0">
                                    <label for="extra_data[{{ str_replace('*', '', $key) }}]">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                    </label>
                                    <input type="{{ $field }}" class="form-control"
                                        name="extra_data[{{ str_replace('*', '', $key) }}]"
                                        {{ str_contains($key, '*') ? 'required' : '' }}>
                                </div>
                            @endif
                            @if (is_array($field))
                                @foreach ($field as $subkey => $subField)
                                    <div class="form-group col-12 p-0">
                                        <label
                                            for="extra_data[{{ $key }}][{{ str_replace('*', '', $subkey) }}]">
                                            {{ ucfirst(str_replace('_', ' ', $subkey)) }}
                                        </label>
                                        <input type="{{ $subField }}" class="form-control"
                                            name="extra_data[{{ $key }}][{{ str_replace('*', '', $subkey) }}]"
                                            {{ str_contains($subKey, '*') ? 'required' : '' }}>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                        @if ($next_step->order_no === 13 && $data->status === 'approved' && $data->cheque)
                            @include('components.form-image-submission', [
                                'id' => 'cheque-image-proof',
                                'name' => 'cheque-image-proof',
                                'required' => true,
                                'label' => 'Proof of Claiming Cheque',
                            ])
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="comments">Comments</label>
                        @include('components.form-textarea', [
                            'id' => 'comments',
                            'name' => 'comments',
                            'required' => false,
                        ])
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script nonce="{{ $nonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('addUpdateModal-{{ $data->id }}');
        const dateIn = modal.querySelector('#date_in-{{ $data->id }}');
        const dateOut = modal.querySelector('#date_out-{{ $data->id }}');

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
