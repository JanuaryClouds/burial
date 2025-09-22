<?php

namespace App\Http\Controllers;

use App\Models\Handler;
use App\Models\WorkflowStep;
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
            return redirect()->back()->with('alertSuccess', Str::ucfirst($request->name) . ' added Successfully');
        }
        if ($type == 'relationships') {
            $data = $request->validate([
                'name' => 'required',
                'remarks' => 'nullable',
            ]);
            Relationship::create($data);
            return redirect()->back()->with('alertSuccess', Str::ucfirst($request->name) . ' added Successfully');
        }
    }

    public function updateContent(Request $request, $type, $id) {
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
        return redirect()->back()->with('alertSuccess', Str::ucfirst($tempName) . ' updated Successfully');
    }

    public function deleteContent(Request $request, $type, $id) {
        if ($type == 'barangays') {
            $barangay = Barangay::findOrFail($id);
            $tempName = $barangay->name;
            if ($barangay) {
                $barangay->delete();                
            } else {
                return redirect()->back()->with('alertError', Str::ucfirst($tempName) . ' not found');
            }
        }
        if ($type == 'relationships') {
            $relationship = Relationship::find($id);
            $tempName = $relationship->name;
            if ($relationship) {
                $relationship->delete();
            } else {
                return redirect()->back()->with('alertError', Str::ucfirst($tempName) . ' not found');
            }
        }
        return redirect()->back()->with('alertSuccess', Str::ucfirst($tempName) . ' deleted Successfully');
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

    public function workflow() {
        $data = WorkflowStep::all();
        $type = 'workflows';
        return view('superadmin.cms', compact('data', 'type'));
    }

    public function handlers() {
        $data = Handler::all();
        $type = 'handlers';
        return view('superadmin.cms', compact('data', 'type'));
    }
}
