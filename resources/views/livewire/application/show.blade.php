<div class="d-flex flex-column gap-6" wire:poll.30s="refreshApplication">
    <div class="row">
        <div class="col-12 col-lg-9" id="application-summary">
            {{-- Application Summary --}}
            @include('application.show.partials.summary')
        </div>
        <div class="col-12 col-lg-3">
            {{-- Page Navigation --}}
            @include('application.partials.navigation')
        </div>
    </div>

    <div class="row">
        <div class="col-12" id="status">
            {{-- Status Timeline --}}
            @include('application.show.partials.status-timeline')
        </div>
    </div>

    @if ($claimantHistory->count() > 1)
        @include('application.show.partials.claimant-history')

    @endif

    @hasanyrole('staff|superadmin')
        <div class="row">
            <div class="col-12 col-lg-6" id="assessment">
                {{-- Assessment --}}
                @include('application.show.partials.assessment')
            </div>
            <div class="col-12 col-lg-6" id="recommendation">
                {{-- Recommendation --}}
                @include('application.show.partials.recommendation')
            </div>
        </div>
        @if ($application->recommendations_count > 0)
            <div class="row">
                <div class="col-12 col-xl-7" id="workflow-history">
                    {{-- Workflow History --}}
                    @include('application.show.partials.workflow.history.index')
                </div>
                <div class="col-12 col-xl-5 d-flex flex-column gap-6" id="workflow-history-create-form">
                    {{-- Create Workflow History --}}
                    @include('application.show.partials.workflow.history.create')
                </div>
            </div>
        @endif
    @endhasanyrole
    <div id="documents" wire:ignore>
        <x-card>
            {{-- Documents --}}
            <x-slot:header>Documents</x-slot:header>
            @include('application.show.partials.documents')
        </x-card>
    </div>
    <div class="d-flex flex-center">
        @if ($application->closure)
            <p class="text-muted">Application has been closed</p>
        @elseif (auth()->user()->can('cancel', $application) ||
                auth()->user()->can('reject', $application) ||
                auth()->user()->can('refer', $application) ||
                auth()->user()->can('close', $application))
            <a href="{{ route('application.close', $application) }}" class="btn btn-danger">
                <x-icon.font-awesome class="fa-stop" />
                Close Application
            </a>
        @endif
    </div>
</div>
