<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function store(BurialServiceProviderRequest $request) {
        $data = $request->validated();
        $provider = $this->BurialServiceProviderService->store($data);

        if ($provider) {
            return redirect()->route('admin.burial.providers')->with('success', 'Burial service provider created successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'Failed to create burial service provider.']);
    }
}
