<x-card>
    <x-slot:header>Application Summary</x-slot:header>
    @if ($inlineApplicationDetails ?? false)
        @include('livewire.application.summary', [
            'client' => $application->client,
            'beneficiary' => $application->beneficiary,
        ])
    @else
        <livewire:application.summary :application="$application" defer />
    @endif
    <x-slot:footer>
        <a href="{{ route('client.show', $application->client) }}" class="btn btn-sm btn-light">
            <x-icon.font-awesome class="fa-up-right-from-square" />
            Show Client
        </a>
        <a href="{{ route('beneficiary.show', $application->beneficiary) }}" class="btn btn-sm btn-light">
            <x-icon.font-awesome class="fa-up-right-from-square" />
            Show Beneficiary
        </a>
        @role('staff')
            <a href="{{ route('application.print', $application) }}" target="_blank" class="btn btn-sm btn-light">
                <x-icon.font-awesome class="fa-print" />
                Print GIS Form
            </a>
        @endrole
        @can('changeClaimant', $application)
            <a href="{{ route('application.change-claimant', $application) }}" class="btn btn-sm btn-warning">
                <x-icon.font-awesome class="fa-user-pen" />
                Change Claimant
            </a>
        @endcan
        <x-modal :modalId="'tracker-slip-modal'" :modalTitle="'Tracker Slip'" :modalSize="'md'" :buttonClass="'btn-sm btn-primary'">
            <x-slot:triggerButton>
                <x-icon.font-awesome class="fa-qrcode" />
                Show Tracker Slip
            </x-slot:triggerButton>
            @include('application.partials.codes', [
                'qrCode' => $qrCode,
                'barcode' => $barcode,
            ])
            <x-slot:footer>
                <a href="{{ route('application.tracker-slip', $application) }}" target="_blank"
                    class="btn btn-sm btn-primary">
                    <x-icon.font-awesome class="fa-print" />
                    Print
                </a>
            </x-slot:footer>
        </x-modal>
    </x-slot:footer>
</x-card>
