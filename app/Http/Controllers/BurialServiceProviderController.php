<?php

namespace App\Http\Controllers;

use App\Models\BurialServiceProvider;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Services\BurialServiceProviderService;
use App\Http\Requests\BurialServiceProviderRequest;

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
}
