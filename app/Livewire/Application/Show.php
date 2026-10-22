<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Models\DocumentRequirement;
use App\Services\ApplicationService;
use Illuminate\Support\Collection;
use Livewire\Component;

class Show extends Component
{
    public Application $application;

    private ApplicationService $services;

    public string $qrCode;

    public string $barcode;

    public Collection $documents;

    public function mount(Application $application, ApplicationService $applicationServices)
    {
        $this->application = $application;
        $this->services = $applicationServices;

        if ($this->application) {
            $this->qrCode = $this->services->getQrCodeUri(
                'svg',
                $application->qr_code,
                200
            );

            $this->barcode = $this->services->getBarcodeUri($application->qr_code);
        }

        $this->documents = DocumentRequirement::index()->get();
    }

    public function render()
    {
        return view('livewire.application.show');
    }
}
