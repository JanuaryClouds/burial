<?php

namespace App\Livewire\Application;

use App\Models\Application;
use Barcode\Facades\Barcode;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Search extends Component
{
    public ?string $code = null;

    public ?Application $application = null;

    public ?string $qrCode = null;

    public ?string $barcode = null;

    public function search()
    {
        $application = Application::where('qr_code', $this->code)->first();

        if ($application) {
            $this->application = $application;

            $qrCodeImage = QrCode::format('svg')
                ->size(200)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($application->qr_code);

            $this->qrCode = 'data:image/svg+xml;base64,' . base64_encode($qrCodeImage);
            $this->barcode = Barcode::generateSvgBase64($application->qr_code);
        }
    }

    public function clear()
    {
        $this->application = null;
        $this->code = '';
    }

    public function render()
    {
        return view('livewire.application.search');
    }
}
