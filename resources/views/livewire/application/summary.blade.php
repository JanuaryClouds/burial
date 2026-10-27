<div>
    <div class="row mb-4">
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column gap-2">
                <span>
                    <strong>Tracking Number:</strong> {{ $application->tracking_no }}
                </span>
                <span>
                    <strong>Submitted on:</strong>
                    {{ \Carbon\Carbon::parse($application->created_at)->format('F d, Y') }}
                </span>
                <span>
                    <strong>Relationship of Client to Beneficiary:</strong>
                    {{ $application->relationship->name }}
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="border border-2 border-dashed border-gray-300 rounded px-4 py-3 mb-4">
                <div class="d-flex flex-column gap-2">
                    <h4>Client</h4>
                    <span>
                        <strong>Name:</strong> {{ $client->fullname() }}
                    </span>
                    <span>
                        <strong>Address:</strong> {{ $client->fullAddress() }}
                    </span>
                    <span>
                        <strong>Contact Number:</strong> {{ $client->contact_number }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="border border-2 border-dashed border-gray-300 rounded px-4 py-3 mb-4">
                <div class="d-flex flex-column gap-2">
                    <h4>Beneficiary</h4>
                    <span>
                        <strong>Name:</strong> {{ $beneficiary->fullname() }}
                    </span>
                    <span>
                        <strong>Date of Birth:</strong>
                        {{ \Carbon\Carbon::parse($beneficiary->date_of_birth)->format('F d, Y') }}
                    </span>
                    <span>
                        <strong>Date of Death:</strong>
                        {{ \Carbon\Carbon::parse($beneficiary->date_of_death)->format('F d, Y') }}
                        ({{ $beneficiary->age() }} years old)
                    </span>
                </div>
            </div>
        </div>
    </div>
    @if ($hasClaimantChanges ?? false)
        <x-callout class="bg-warning-subtle border-warning rounded text-body" role="status">
            <x-slot:icon>
                <x-icon.font-awesome class="fa-triangle-exclamation text-warning fs-2" />
            </x-slot:icon>
            <x-slot:title>
                <strong class="d-block mb-2">The claimant for this application has changed</strong>
            </x-slot:title>
            Refer to Claimant History for the former and current claimants and the replacement details.
            @can('office-access')
                <span class="d-block mt-2">Review the retained documents before continuing processing.</span>
            @endcan
            <div class="mt-3">
                @if (Route::currentRouteName() === 'application.show')
                    <a href="#claimant-history" class="btn btn-sm btn-warning">View Claimant History</a>
                @else
                    <a href="{{ route('application.show', $application) }}#claimant-history"
                        class="btn btn-sm btn-warning">View Claimant History</a>
                @endif
            </div>
        </x-callout>
    @endif
</div>
