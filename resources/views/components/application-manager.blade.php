@props([
    'application' => [],
])
@if ($processLogs->count() == 0 || $application->status != 'released')
    @if ($application->claimantChanges->count() == 0 || $claimantChange->status != 'pending')
        <div class="bg-body shadow-sm p-4">
            <div class="d-flex justify-content-end gap-3">
                <!-- TODO: Make these two links admin-only -->
                <a href="{{ route('clients.gis-form', ['id' => $application->claimant->client_id]) }}"
                    class="btn btn-light mr-2" data-no-loader>
                    Generate GIS Form
                </a>
                <a href="{{ route('burial-assistances.certificate', ['id' => $application->id]) }}"
                    class="btn btn-light mr-2" target="_blank">
                    Download Certificate
                </a>
                @if (app()->isLocal())
                    <a href="{{ route('burial.tracker', ['uuid' => $application->id]) }}" class="btn btn-light mr-2"
                        target="_blank">
                        <i class="fas fa-eye"></i>
                        View as Guest
                    </a>
                    @php
                        $logs = $application->processLogs->sortBy('created_at');
                    @endphp
                    @if ($application->claimantChanges->isEmpty())
                        <button class="btn btn-secondary mr-2" type="button" data-toggle="modal"
                            data-target="#changeClaimantModal">
                            <i class="fas fa-helmet-safety"></i>
                            Change Claimant
                        </button>
                        <x-change-claimant-modal :burialAssistance="$application" />
                    @endif
                @endif
                @can('reject-applications')
                    @if ($application->status != 'rejected')
                        <button class="btn btn-primary mr-2" type="button" data-toggle="modal"
                            data-target="#addUpdateModal-{{ $application->id }}">
                            <i class="fas fa-plus"></i>
                            Add Progress Update
                        </button>
                        <button class="btn btn-danger" type="button" data-toggle="modal"
                            data-target="#reject-{{ $application->id }}">
                            <i class="fas fa-times-circle"></i>
                            Reject Application
                        </button>
                    @endif
                    @if ($application->status == 'rejected')
                        <button class="btn btn-success" type="button" data-toggle="modal"
                            data-target="#reject-{{ $application->id }}">
                            <i class="fas fa-rotate-left"></i>
                            Restore Application
                        </button>
                    @endif
                @endcan
            </div>
        </div>
    @elseif ($application->claimantChanges->count() > 0 && $claimantChange->status == 'pending')
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Request for Claimant Change</h4>
            </div>
            <div class="card-body">
                <p>A change of claimants have been requested for this application. Please contact the original
                    claimant for confirmation.</p>
                <x-form-input name="reason_for_change" id="reason_for_change" label="Reason for Change" placeholder=""
                    value="{{ $claimantChange->reason_for_change }}" readonly="true" disabled="true" />
                <x-claimant-form :claimant="$claimantChange->newClaimant" :readonly="true" :disabled="true" />
                <form
                    action="{{ route('burial.claimant-change.decision', [
                        'uuid' => $application->id,
                        'change' => $claimantChange->id,
                    ]) }}"
                    method="post">
                    @csrf
                    <div class="d-flex justify-content-end gap-2 mt-2 align-items-center">
                        <x-form-select name="decision" id="decision" :options="['approved' => 'Approve', 'rejected' => 'Reject']" />
                        <div class="mb-3">
                            <button class="btn btn-success ml-2" type="submit">Submit Decision</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endif
