<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BurialAssistanceRequest;
use App\Models\BurialService;
use App\Models\BurialServiceProvider;

class SearchController extends Controller
{
    public function search(Request $request)
{
    $term = $request->get('q', '');
    if (!$term) {
        return response()->json([]);
    }

    // Search Burial Assistance Requests
    $requests = BurialAssistanceRequest::with(['barangay', 'service'])
        ->where(function ($q) use ($term) {
            $q->where('deceased_firstname', 'like', "%{$term}%")
              ->orWhere('deceased_lastname', 'like', "%{$term}%")
              ->orWhere('representative', 'like', "%{$term}%")
              ->orWhere('representative_contact', 'like', "%{$term}%")
              ->orWhere('burial_address', 'like', "%{$term}%")
              ->orWhere('remarks', 'like', "%{$term}%");
        })
        ->take(5)
        ->get()
        ->map(fn($r) => [
            'type' => 'requests',
            'deceased_firstname' => $r->deceased_firstname,
            'deceased_lastname' => $r->deceased_lastname,
            'representative' => $r->representative,
            'representative_contact' => $r->representative_contact,
            'burial_address' => $r->burial_address,
            'barangay' => optional($r->barangay)->name,
            'start_of_burial' => $r->start_of_burial,
            'end_of_burial' => $r->end_of_burial,
            'service' => optional($r->service)->name,
            'remarks' => $r->remarks,
            'url' => route('admin.burial.request.view', ['uuid' => $r->uuid]),
        ]);
    return response()->json($requests);
}


}
