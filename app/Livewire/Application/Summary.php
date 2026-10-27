<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Services\ApplicationService;
use App\Traits\Livewire\GuardsApplicationView;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Summary extends Component
{
    use GuardsApplicationView;
    use HasPlaceholder;

    public Application $application;

    private ApplicationService $services;

    public Client $client;

    public Beneficiary $beneficiary;

    public mixed $qrCode;

    public mixed $barcode;

    public function boot(ApplicationService $applicationService)
    {
        $this->services = $applicationService;
    }

    public function mount(Application $application)
    {
        $application->loadMissing(['client.user', 'client.address', 'beneficiary', 'relationship']);
        $this->application = $application;
        $this->client = $application->client;
        $this->beneficiary = $application->beneficiary;
        $this->qrCode = $this->services->getQrCodeUri('svg', $application->qr_code, 200);
        $this->barcode = $this->services->getBarcodeUri($application->qr_code);
    }

    public function render()
    {
        $this->application->loadCount('claimantAssignments');

        return view('livewire.application.summary', ['hasClaimantChanges' => $this->application->claimant_assignments_count > 1]);
    }
}
