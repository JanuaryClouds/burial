<?php

namespace App\Livewire\Application;

use App\Models\Application;
use Barcode\Facades\Barcode;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Search extends Component
{
    public function boot(): void
    {
        $this->authorize('search-applications');
    }

    public ?string $code = null;

    public ?Application $application = null;

    public ?string $qrCode = null;

    public ?string $barcode = null;

    public function search()
    {
        $this->reset('application', 'qrCode', 'barcode');
        $this->resetValidation();
        $this->validate(['code' => 'required|string|max:100']);
        $code = trim($this->code);
        $application = Application::where('qr_code', $code)->orWhere('tracking_no', $code)->first();

        if (! $application || auth()->user()->cannot('view', $application)) {
            $this->addError('code', 'No accessible application was found for this code.');

            return;
        }

        if ($application) {
            $this->application = $application;

            $qrCodeImage = QrCode::format('svg')
                ->size(200)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($application->qr_code);

            $this->qrCode = 'data:image/svg+xml;base64,'.base64_encode($qrCodeImage);
            $this->barcode = Barcode::generateSvgBase64($application->qr_code);
        }
    }

    public function clear()
    {
        $this->reset('application', 'qrCode', 'barcode');
        $this->resetValidation();
        $this->code = '';
    }

    public function render()
    {
        if ($this->application && auth()->user()->cannot('view', $this->application->fresh())) {
            $this->reset('application', 'qrCode', 'barcode');
        }

        return view('livewire.application.search');
    }
}
