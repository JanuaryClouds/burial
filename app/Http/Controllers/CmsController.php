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
        }

        // TODO: Add delete snippet and update key
        return redirect()->back()->with('success', Str::ucfirst($tempName) . ' updated Successfully');
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

    public function burialAssistanceRequests() {
        $data = BurialAssistanceRequest::all();
        $type = 'requests';
        $fields = [

        ];
        return view('superadmin.cms', compact('data', 'type'));
    }

    public function burialServices() {
        $data = BurialService::all();
        $type = 'services';
        return view('superadmin.cms', compact('data', 'type'));
    }
    
    public function burialServiceProviders() {
        $data = BurialServiceProvider::all();
        $type = 'providers';
        return view('superadmin.cms', compact('data', 'type'));
    }
}
