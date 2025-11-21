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
use Spatie\Permission\Models\Role;
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
            if ($type == 'roles') {
                dd($request->all());
                $data = $request->validate([
                    'name' => 'required|string|max:255',
                ]);
                $data['guard_name'] = 'web';
                // Role::create($data);
                return redirect()->back()->with('alertSuccess', Str::ucfirst($request->name) . ' added Successfully');
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
                $role = Role::where('name', $request->role)->get()->first();
                // $user->is_active = !$user->is_active;
                $user->syncRoles([$role]);
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
            if ($type == 'roles') {
                $role = Role::find($id);
                $role->name = $request->name;
                $role->update();
                $role->syncPermissions($request->permissions);
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
            if ($type == 'roles') {
                $role = Role::find($id);
                $tempName = $role->name;
                if ($role) {
                    $role->delete();
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
        $page_title = 'CMS - Barangays';
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
            return view('superadmin.cms', compact([
                'data',
                'page_title', 
                'fields', 
                'districts', 
                'type', 
            ]));
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());
        }
    }

    public function relationships() {
        $page_title = 'CMS - Relationships';
        try {
            $data = Relationship::select('id', 'name', 'remarks')->get();
            $type = 'relationships';
            $fields = [
                'name' => ['label' => 'name', 'type' => 'text'],
                'remarks' => ['label' => 'name', 'type' => 'text'],
            ];
    
            return view('superadmin.cms', compact('data', 'page_title', 'type', 'fields'));
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());            
        }
    }

    public function workflow() {
        $page_title = 'CMS - Workflow';
        $data = WorkflowStep::select('id', 'order_no', 'description')->get();
        $type = 'workflows';
        return view('superadmin.cms', compact('data', 'page_title', 'type'));
    }

    public function handlers() {
        $page_title = 'CMS - Handlers';
        $data = Handler::select('id', 'name', 'type', 'department')->get();
        $type = 'handlers';
        return view('superadmin.cms', compact('data', 'page_title', 'type'));
    }

    public function users() {
        $page_title = 'CMS - Users';
        $data = User::select('id', 'first_name', 'middle_name', 'last_name', 'email', 'password', 'contact_number', 'is_active')->get();
        $type = 'users';
        return view('superadmin.cms', compact('data', 'page_title', 'type'));
    }

    public function religions() {
        $page_title = 'CMS - Relitions';
        $data = Religion::select('id', 'name', 'remarks')->get();
        $type = 'religions';
        return view('superadmin.cms', compact('data', 'page_title', 'type'));
    }
}
