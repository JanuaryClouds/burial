<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\DataTables\CmsDataTable;
use App\Services\RoleService;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    protected $roleServices;

    public function __construct(RoleService $roleServices)
    {
        $this->roleServices = $roleServices;
    }

    // boilerplate code
    // public function index(CmsDataTable $dataTable)
    // {
    //     $page_title = 'Role';
    //     $resource = 'role';
    //     $columns = ['id', 'name', 'guard', 'action'];
    //     $data = Role::getAllRoles();
        
    //     return $dataTable
    //         ->render('cms.index', compact(
    //             'dataTable',
    //             'page_title',
    //             'resource',
    //             'columns',
    //             'data',
    //         ));
    // }

    public function index() {
        $data = Role::getAllRoles();
        $type = 'roles';
        return view('superadmin.roles', compact('data', 'type'));
    }
    
    public function store(RoleRequest $request)
    {
        $request['guard_name'] = 'web';
        $role = $this->roleServices->storeRole($request->validated());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->log('Created a new role: ' . $role->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.role.index')
            ->with('success', 'Role created successfully.');
    }
    
    public function update(RoleRequest $request, Role $role)
    {
        $role = $this->roleServices->updateRole($request->validated(), $role);

        $role->syncPermissions($request->permissions);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->log('Updated the role: ' . $role->name);
            
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.role.index')
            ->with('success', 'Role updated successfully.');
    }
    
    public function destroy(Role $role)
    {
        $role = $this->roleServices->deleteRole($role);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->log('Deleted the role: ' . $role->name);
            
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.role.index')
            ->with('success', 'Role deleted successfully.');
    }
}