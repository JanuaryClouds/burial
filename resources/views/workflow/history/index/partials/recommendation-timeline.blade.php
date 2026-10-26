@foreach ($recommendations as $recommendation)
    <div class="timeline-item">
        <div class="timeline-label">
            <span
                class="text-uppercase fw-bold">{{ \Carbon\Carbon::parse($recommendation->created_at)->format('H:i') }}</span>
            <br>
            <span
                class="text-uppercase small text-muted fw-semibold">{{ \Carbon\Carbon::parse($recommendation->created_at)->format('M d') }}</span>
        </div>
        <div class="timeline-badge">
            <i class="fa-solid fa-genderless text-primary fs-1"></i>
        </div>
        <div
            class="timeline-content ms-3 d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 w-100">
            <div class="text-uppercase fw-bold text-break">
                Recommendation: {{ $recommendation->funeralAssistanceType?->name ?? 'N/A' }}
                <span
                    class="d-block text-muted small">{{ $recommendation->workflow?->label() ?? 'Unassigned legacy workflow' }}</span>
            </div>
            <div class="d-flex align-items-center gap-4 flex-shrink-0">
                <button type="button"
                    class="btn btn-sm btn-link p-0 d-inline-flex align-items-center gap-1 text-primary"
                    data-bs-toggle="modal" data-bs-target="#showRemarksModal"
                    wire:click="showRemarks('{{ class_basename($recommendation) }}','{{ $recommendation->uuid }}')">
                    <x-icon.font-awesome class="fa-comment-dots p-0 m-0" />
                    Remarks
                    @if ($recommendation->remarks->isNotEmpty())
                        ({{ $recommendation->remarks->count() }})
                    @endif
                </button>
            </div>
        </div>
    </div>
    @foreach ($recommendation->workflowHistory as $history)
        @if ($history->toStage)
            <div class="timeline-item">
                <div class="timeline-label">
                    <span
                        class="text-uppercase fw-bold">{{ \Carbon\Carbon::parse($history->date_in)->format('H:i') }}</span>
                    <br>
                    <span
                        class="text-uppercase small text-muted fw-semibold">{{ \Carbon\Carbon::parse($history->date_in)->format('M d') }}</span>
                </div>
                <div class="timeline-badge">
                    <i class="fa-solid fa-genderless text-primary fs-1"></i>
                </div>
                <div
                    class="timeline-content ms-3 d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 w-100">
                    <span class="text-uppercase fw-bold text-break">{{ $history->toStage->name }}</span>
                    <div class="d-flex align-items-center gap-4 flex-shrink-0">
                        @if ($history->toStage->custom_fields_count > 0 || $history->custom_values_count > 0)
                            <button type="button"
                                class="btn btn-sm btn-link p-0 d-inline-flex align-items-center gap-1 text-primary"
                                data-bs-toggle="modal" data-bs-target="#showHistoryDetailsModal"
                                wire:click="showHistoryDetails('{{ $history->uuid }}')">
                                <x-icon.font-awesome class="fa-list-ul p-0 m-0" />
                                Details
                            </button>
                        @endif
                        <button type="button"
                            class="btn btn-sm btn-link p-0 d-inline-flex align-items-center gap-1 text-primary"
                            data-bs-toggle="modal" data-bs-target="#showRemarksModal"
                            wire:click="showRemarks('{{ class_basename($history) }}','{{ $history->uuid }}')">
                            <x-icon.font-awesome class="fa-comment-dots p-0 m-0" />
                            Remarks
                            @if ($history->remarks->isNotEmpty())
                                ({{ $history->remarks->count() }})
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endforeach
