<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\BurialAssistanceRequest;
use App\Models\BurialService;
use App\Models\BurialServiceProvider;
use App\Models\Relationship;
use App\Models\District;
use Str;

class CmsController extends Controller
{
    public function storeContent(Request $request, $type) {
        if ($type == 'barangays') {
            $data = $request->validate([
                'name' => 'required',
                'district_id' => 'required',
                'remarks' => 'nullable',
            ]);
            Barangay::create($data);
            return redirect()->back()->with('success', Str::ucfirst($request->name) . ' added Successfully');
        }
        if ($type == 'relationships') {
            $data = $request->validate([
                'name' => 'required',
                'remarks' => 'nullable',
            ]);
            Relationship::create($data);
            return redirect()->back()->with('success', Str::ucfirst($request->name) . ' added Successfully');
        }
    }

    public function updateContent(Request $request, $type, $id) {
        if ($request->action === 'update') {
            if ($type == 'barangays') {
                $barangay = Barangay::find($id);
                $barangay->name = $request->name;
                $barangay->district_id = $request->district_id;
                $barangay->remarks = $request->remarks;
                $barangay->save();
                $tempName = $request->name;
            }
            if ($type == 'relationships') {
                $relationship = Relationship::find($id);
                $relationship->name = $request->name;
                $relationship->remarks = $request->remarks;
                $relationship->save();
                $tempName = $request->name;
            }
            return redirect()->back()->with('success', Str::ucfirst($tempName) . ' updated Successfully');
        }

        if ($request->action === 'delete') {
            if ($type == 'barangays') {
                $tempName = $request->name;
                Barangay::find($id)->delete();
            }
            if ($type == 'relationships') {
                $tempName = $request->name;
                Relationship::find($id)->delete();
            }
            if ($type == 'requests') {
                $tempData = BurialAssistanceRequest::find($id);
                $tempName = $tempData->deceased_firstname . " " .  $tempData->deceased_lastname . "'s request has";
                BurialAssistanceRequest::find($id)->delete();
            }
            if ($type == 'providers') {
                $tempName = $request->name;
                BurialServiceProvider::find($id)->delete();
            }
            if ($type == 'services') {
                $tempData = BurialService::find($id);
                $tempName = $tempData->deceased_firstname . " " .  $tempData->deceased_lastname . "'s burial service has";
                BurialService::find($id)->delete();
            }

            return redirect()->back()->with('success', Str::ucfirst($tempName) . ' deleted Successfully');
        }
    }

    public function deleteContent(Request $request, $type, $id) {
        dd($request->name);
        if ($type == 'barangays') {
            $tempName = $request->name;
            Barangay::find($id)->delete();
        }
        if ($type == 'relationships') {
            $tempName = $request->name;
            Relationship::find($id)->delete();
        }

        return redirect()->back()->with('success', Str::ucfirst($tempName) . ' deleted Successfully');
    }

    public function barangays() {
        $data = Barangay::all()->sortBy('name');
        $districts = District::all();
        $type = 'barangays';
        $fields = [
            'name' => ['label' => 'name', 'type' => 'text'],
            'district_id' => [
                'label' => 'district_id', 
                'type' => 'select', 
                'options' => $districts
            ],
            'remarks' => ['label' => 'remarks', 'type' => 'text'],
        ];
        return view('superadmin.cms', compact(
            ['data', 
            'fields', 
            'districts', 
            'type', 
        ]));
    }

    public function relationships() {
        $data = Relationship::all();
        $type = 'relationships';
        $fields = [
            'name' => ['label' => 'name', 'type' => 'text'],
            'remarks' => ['label' => 'name', 'type' => 'text'],
        ];

        return view('superadmin.cms', compact('data', 'type', 'fields'));
    }

    public function burialAssistanceRequests() {
        $data = BurialAssistanceRequest::all();
        $type = 'requests';
        $fields = [
            'uuid' => ['label' => 'uuid', 'type' => 'text'],
            'deceased_lastname' => ['label' => 'deceased_lastname', 'type' => 'text'],
            'deceased_firstname' => ['label' => 'deceased_firstname', 'type' => 'text'],
            'status' => [
                'label' => 'status',
                'type' => 'select',
                'options' => [
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'declined' => 'Declined',
                ]
            ],
        ];
        return view('superadmin.cms', compact('data', 'type', 'fields'));
    }

    public function burialServices() {
        $data = BurialService::all();
        $type = 'services';
        $fields = [
            'deceased_lastname' => ['label' => 'deceased_lastname', 'type' => 'text'],
            'deceased_firstname' => ['label' => 'deceased_firstname', 'type' => 'text'],
        ];
        return view('superadmin.cms', compact('data', 'type', 'fields'));
    }
    
    public function burialServiceProviders() {
        $data = BurialServiceProvider::all();
        $type = 'providers';
        $barangays = Barangay::all();
        $fields = [
            'name' => ['label' => 'name', 'type' => 'text'],
            'address' => ['label' => 'address', 'type' => 'text'],
            'barangay_id' => [
                'label' => 'barangay_id',
                'type' => 'select',
                'options' => $barangays
            ],
        ];
        return view('superadmin.cms', compact('data', 'type', 'fields', 'barangays'));
    }
}
