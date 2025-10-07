<?php

namespace App\Http\Controllers;

use App\Models\Handler;
use App\Models\Religion;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\BurialAssistanceRequest;
use App\Models\BurialService;
use App\Models\BurialServiceProvider;
use App\Models\Relationship;
use App\Models\District;
use App\Models\User;
use Exception;
use Str;

class CmsController extends Controller
{
    public function storeContent(Request $request, $type) {
        try {
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
            if ($type == 'users') {
                $data = $request->validate([
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'middle_name' => 'nullable|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'contact_number' => 'required|string|max:11',
                    'password' => 'required|string|min:8',
                ]);
                $user = User::create($data);
                $user->assignRole('admin');
                return redirect()->back()->with('alertSuccess', Str::ucfirst($type) . ' added Successfully');
            }
            if ($type == 'religions') {
                $data = $request->validate([
                    'name' => 'required|string|max:255',
                    'remarks' => 'nullable|string|max:255',
                ]);
                $religion = Religion::create($data);
                return redirect()->back()->with('alertSuccess', Str::ucfirst($religion->name) . ' added Successfully');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());
        }
    }

    public function updateContent(Request $request, $type, $id) {
        try {
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
            if ($type == 'users') {
                $user = User::find($id);
                $user->is_active = !$user->is_active;
                $user->save();
                $tempName = $user->first_name . ' ' . $user->last_name;
            }
            if ($type == 'religions') {
                $religion = Religion::find($id);
                $religion->name = $request->name;
                $religion->remarks = $request->remarks;
                $religion->save();
                $tempName = $request->name;
            }
            return redirect()->back()->with('alertSuccess', Str::ucfirst($tempName) . ' updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());
        }
    }

    public function deleteContent(Request $request, $type, $id) {
        try {
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
            if ($type == 'religions') {
                $religion = Religion::find($id);
                $tempName = $religion->name;
                if ($religion) {
                    $religion->delete();
                } else {
                    return redirect()->back()->with('alertError', Str::ucfirst($tempName) . ' not found');
                }
            }
            return redirect()->back()->with('alertSuccess', Str::ucfirst($tempName) . ' deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());
        }
    }

    public function barangays() {
        try {
            $data = Barangay::select('id', 'name', 'district_id', 'remarks')
                ->get();
            $districts = District::select('id', 'name')->get();
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
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());
        }
    }

    public function relationships() {
        try {
            $data = Relationship::select('id', 'name', 'remarks')->get();
            $type = 'relationships';
            $fields = [
                'name' => ['label' => 'name', 'type' => 'text'],
                'remarks' => ['label' => 'name', 'type' => 'text'],
            ];
    
            return view('superadmin.cms', compact('data', 'type', 'fields'));
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());            
        }
    }

    // ! Depracated
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

    // ! Depracated
    public function burialServices() {
        $data = BurialService::all();
        $type = 'services';
        $fields = [
            'deceased_lastname' => ['label' => 'deceased_lastname', 'type' => 'text'],
            'deceased_firstname' => ['label' => 'deceased_firstname', 'type' => 'text'],
        ];
        return view('superadmin.cms', compact('data', 'type', 'fields'));
    }
    
    // ! Depracated
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
        $data = WorkflowStep::select('id', 'order_no', 'description')->get();
        $type = 'workflows';
        return view('superadmin.cms', compact('data', 'type'));
    }

    public function handlers() {
        $data = Handler::select('id', 'name', 'type', 'department')->get();
        $type = 'handlers';
        return view('superadmin.cms', compact('data', 'type'));
    }

    public function users() {
        $data = User::select('id', 'first_name', 'middle_name', 'last_name', 'email', 'password', 'contact_number', 'is_active')->get();
        $type = 'users';
        return view('superadmin.cms', compact('data', 'type'));
    }

    public function religions() {
        $data = Religion::select('id', 'name', 'remarks')->get();
        $type = 'religions';
        return view('superadmin.cms', compact('data', 'type'));
    }
}
