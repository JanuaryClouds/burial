<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Models\Client;
use App\Models\Beneficiary;
use App\Services\ApplicationService;
use Livewire\Component;

class Summary extends Component
{
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
        $this->application = $application;
        $this->client = $application->client;
        $this->beneficiary = $application->beneficiary;
        $this->qrCode = $this->services->getQrCodeUri('svg', $application->qr_code, 200);
        $this->barcode = $this->services->getBarcodeUri($application->qr_code);
    }

    public function render()
    {
        return view('livewire.application.summary');
    }
}
