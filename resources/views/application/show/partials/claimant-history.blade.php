<x-card id="claimant-history">
    <x-slot:header>Claimant History</x-slot:header>
    <div class="row g-4">
        @foreach ($claimantHistory as $assignment)
            <div class="col-12 col-lg-6" wire:key="claimant-{{ $assignment->uuid }}">
                <div class="border border-2 border-dashed border-gray-300 rounded p-4 h-100 d-flex flex-column gap-3 text-break">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h4 class="mb-0">{{ $assignment->identity_snapshot['name'] ?? 'Client' }}</h4>
                        <span class="badge {{ $assignment->ended_at ? 'bg-secondary' : 'bg-success' }}">{{ $assignment->ended_at ? 'Former claimant' : 'Current claimant' }}</span>
                    </div>
                    <p class="mb-0"><strong>Claimant Since:</strong> {{ $assignment->started_at->format('d M Y H:i') }}</p>
                    @if ($assignment->ended_at)
                        <p class="mb-0"><strong>Replaced On:</strong> {{ $assignment->ended_at->format('d M Y H:i') }}</p>
                    @endif
                    <p class="mb-0"><strong>Relationship:</strong> {{ $assignment->relationship?->name ?? 'Not recorded' }}</p>
                    @if ($assignment->assignedBy)
                        <p class="mb-0"><strong>Changed By:</strong> {{ $assignment->assignedBy->fullname() }}</p>
                    @endif
                    @if ($assignment->reason)
                        <p class="mb-0"><strong>Reason:</strong> {{ $assignment->reason }}</p>
                    @endif
                    @if ($assignment->ended_at && auth()->user()->hasAnyRole(['staff', 'superadmin']))
                        <div class="border-top pt-3 mt-1 d-flex flex-column gap-3">
                            <h5 class="mb-0">Previous processing cycle <span class="text-muted fs-7">(read-only)</span></h5>
                            @foreach ($assignment->interviews as $interview)
                                <p class="mb-0"><strong>Interview:</strong> {{ \Carbon\Carbon::parse($interview->schedule)->format('d M Y H:i') }}<br>
                                    <strong>Status:</strong> {{ ucfirst($interview->status) }}</p>
                            @endforeach
                            @foreach ($assignment->assessments as $assessment)
                                <div class="d-flex flex-column gap-2">
                                    <p class="mb-0"><strong>Problem Presented:</strong> {{ $assessment->problem_presented }}</p>
                                    <p class="mb-0"><strong>Assessment:</strong> {{ $assessment->swa }}</p>
                                </div>
                            @endforeach
                            @foreach ($assignment->recommendations as $recommendation)
                                <div class="d-flex flex-column gap-2">
                                    <p class="mb-0"><strong>Recommendation:</strong> {{ $recommendation->workflow?->name ?? 'Legacy workflow' }}</p>
                                    <p class="mb-0"><strong>Amount Extended:</strong> {{ $recommendation->amount_extended }}</p>
                                    <p class="mb-0"><strong>Status:</strong> {{ ucfirst($recommendation->status) }}</p>
                                </div>
                            @endforeach
                            <p class="text-muted mb-0">Detailed workflow history remains available in Process History.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-card>
