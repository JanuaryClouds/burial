<?php

namespace App\Http\Controllers;

use App\Models\BurialServiceProvider;
use Exception;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Services\BurialServiceProviderService;
use App\Http\Requests\BurialServiceProviderRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BurialServiceProviderExport;
use Maatwebsite\Excel\Facades\Excel;
use function Laravel\Prompts\error;

class BurialServiceProviderController extends Controller
{
    protected $BurialServiceProviderService;

    public function __construct(BurialServiceProviderService $burialServiceProviderService) {
        $this->BurialServiceProviderService = $burialServiceProviderService;
    }

    public function newProvider() {
        return view('admin.newBurialServiceProvider');
    }

    public function view($id) {
        $serviceProvider = BurialServiceProvider::where('id', $id)->first();
        $barangays = Barangay::getAllBarangays();
        if (!$serviceProvider) {
            return redirect()->route('admin.burial.providers')->with('error','Burial service provider not found.');
        }
        return view('admin.update-provider', compact('serviceProvider', 'barangays'));
    }

    public function store(BurialServiceProviderRequest $request) {
        $data = $request->validated();
        $provider = $this->BurialServiceProviderService->store($data);

        if ($provider) {
            return redirect()->route('admin.burial.providers')->with('success', 'Burial service provider created successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Failed to create burial service provider.']);
    }

    public function update(BurialServiceProviderRequest $request, $id) {
        $data = $request->validated();
        $provider = BurialServiceProvider::find($id);
        if (!$provider) {
            return redirect()->route('admin.burial.providers')->with('error','Burial service provider not found.');
        }
        $provider->update($data);
        return redirect()->route('admin.burial.providers')->with('success','Successfully updated the provider\'s information.');
    }

    // ! Add messaging API via email or SMS
    public function contact($id) {
        $success = true; //placeholder

        if ($success) {
            return redirect()->route('admin.burial.providers')->with('success', 'Successfully messaged burial service provider.');
        }

        return redirect()->route('admin.burial.providers')->with('error', 'Failed to message burial service provider.');
    }

    public function exportPdf($id) {
        $provider = BurialServiceProvider::findOrFail($id);
        $barangays = Barangay::getAllBarangays();
        $pdf = Pdf::loadView('admin.printable-provider-form', compact('provider', 'barangays'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("{$provider->name}-burial-service-provider-form.pdf");
    }

    // ! returns an error
    public function exportXslx() {
        try {
            return Excel::download(new BurialServiceProviderExport(), 'burial_service_providers.csv');
        } catch (Exception $e) {
            return redirect()->route('admin.burial.providers')->with('error', $e->getMessage());
        }
    }
}
