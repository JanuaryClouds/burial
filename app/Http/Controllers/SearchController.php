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
        $requests = BurialAssistanceRequest::with(['barangay', 'service', 'relationship'])
            ->where(function ($q) use ($term) {
                $q->where('deceased_firstname', 'like', "%{$term}%")
                ->orWhere('deceased_lastname', 'like', "%{$term}%")
                ->orWhere('representative', 'like', "%{$term}%")
                ->orWhere('representative_contact', 'like', "%{$term}%")
                ->orWhere('rep_relationship', 'like', "%{$term}%")
                ->orWhere('barangay_id', 'like', "%{$term}%")
                ->orWhere('burial_address', 'like', "%{$term}%")
                ->orWhere('start_of_burial', 'like', "%{$term}%")
                ->orWhere('end_of_burial', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%")
                ->orWhere('service_id', 'like', "%{$term}%")
                ->orWhere('remarks', 'like', "%{$term}%");
            })
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'type' => 'Requests',
                'deceased_firstname' => $r->deceased_firstname,
                'deceased_lastname' => $r->deceased_lastname,
                'representative' => $r->representative,
                'representative_contact' => $r->representative_contact,
                'barangay' => optional($r->barangay)->name,
                'burial_address' => $r->burial_address,
                'start_of_burial' => $r->start_of_burial,
                'end_of_burial' => $r->end_of_burial,
                'status' => $r->status,
                'service' => optional($r->service)->name,
                'remarks' => $r->remarks,
                'url' => route('admin.burial.request.view', ['uuid' => $r->uuid]),
            ]);
        $services = BurialService::with(['barangay', 'provider',  'relationship'])
            ->where(function ($s) use ($term) {
                $s->where('deceased_firstname', 'like', "%{$term}%")
                ->orWhere('deceased_lastname', 'like', "%{$term}%")
                ->orWhere('representative', 'like', "%{$term}%")
                ->orWhere('representative_contact', 'like', "%{$term}%")
                ->orWhere('rep_relationship', 'like', "%{$term}%")
                ->orWhere('burial_address', 'like', "%{$term}%")
                ->orWhere('barangay_id', 'like', "%{$term}%")
                ->orWhere('start_of_burial', 'like', "%{$term}%")
                ->orWhere('end_of_burial', 'like', "%{$term}%")
                ->orWhere('burial_service_provider', 'like', "%{$term}%")
                ->orWhere('collected_funds', 'like', "%{$term}%")
                ->orWhere('remarks', 'like', "%{$term}%");
            })
            ->take(5)
            ->get()
            ->map(fn($s) => [
                'type' => 'Services',
                'deceased_firstname' => $s->deceased_firstname,
                'deceased_lastname' => $s->deceased_lastname,
                'representative' => $s->representative,
                'representative_contact' => $s->representative_contact,
                'burial_address' => $s->burial_address,
                'barangay' => optional($s->barangay)->name,
                'start_of_burial' => $s->start_of_burial,
                'end_of_burial' => $s->end_of_burial,
                'provider' => optional($s->provider)->name,
                'collected_funds' => $s->remarks,
                'remarks' => $s->remarks,
                'url' => route('admin.burial.view', ['id' => $s->id]),
            ]);
        $providers = BurialServiceProvider::with(['barangay'])
            ->where(function ($p) use ($term) {
                $p->where('name', 'like', "%{$term}%")
                ->orWhere('contact_details', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%")
                ->orWhere('barangay_id', 'like', "%{$term}%")
                ->orWhere('remarks', 'like', "%{$term}%");
            })
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'type' => 'Providers',
                'name' => $p->name,
                'contact' => $p->contact_details,
                'address' => $p->address,
                'barangay' => $p->barangay_id,
                'remarks' => $p->remarks,
                'url' => route('admin.burial.provider.view', ['id' => $p->id]),
            ]);

        $results = $requests->concat($services)->concat($providers);
        return response()->json($results);
    }
}
