@props([
    'application' => []
])
@if ($processLogs->count() == 0 || ($application->status != 'rejected' && $application->status != 'released'))
    @if ($application->claimantChanges->count() == 0 || $claimantChange->status != 'pending')
        <div class="bg-white rounded shadow-sm p-4">
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary mr-2" type="button" data-toggle="modal" data-target="#add-process">Add Progress Update</button>
                <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#confirm-rejection">Reject Application</button>
            </div>
        </div>
    @elseif ($application->claimantChanges->count() > 0 && $claimantChange->status == 'pending')
        <div class="bg-white rounded shadow-sm p-4">
            <section class="section">
                <div class="section-title">
                    <h3>Request for Claimant Change</h3>
                </div>
                <div class="section-lead">
                    <p>A change of claimants have been requested for this application. Please contact the original claimant for confirmation.</p>
                </div>
            </section>
            <x-form-input name="reason_for_change" id="reason_for_change" label="Reason for Change" placeholder="" value="{{ $claimantChange->reason_for_change }}" readonly="true" disabled="true" />
            <x-claimant-form :claimant="$claimantChange->newClaimant" :readonly="true" :disabled="true" />
            <form action="{{ route('admin.application.claimant-change.decision', [
                'id' => $application->id,
                'change' => $claimantChange->id
            ]) }}" method="post">
                @csrf
                <div class="d-flex justify-content-end mt-2 align-items-center">
                    <x-form-select name="decision" id="decision" :options="['approved' => 'Approve', 'rejected' => 'Reject']" />
                    <div class="mb-3">
                        <button class="btn btn-success ml-2" type="submit">Submit Decision</button>
                    </div>
                </div>
            </form>
        </div>
    @endif
@endif
