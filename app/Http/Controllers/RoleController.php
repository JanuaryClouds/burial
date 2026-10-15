<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\RoleRequest;
use App\Services\DatatableService;
use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleServices,
        protected DatatableService $datatableService
    ) {}

    // boilerplate code
    // public function index(CmsDataTable $dataTable)
    // {
    //     $pageTitle = 'Role';
    //     $resource = 'role';
    //     $columns = ['id', 'name', 'guard', 'action'];
    //     $data = Role::getAllRoles();

    //     return $dataTable
    //         ->render('cms.index', compact(
    //             'dataTable',
    //             'pageTitle',
    //             'resource',
    //             'columns',
    //             'data',
    //         ));
    // }

    public function index()
    {
        $data = Role::select('id', 'name')
            ->whereNotIn('name', ['superadmin', 'staff'])
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'show_route' => route('role.edit', $role->id),
                ];
            });

        if (request()->expectsJson()) {
            return $this->datatableService->ajax($data);
        }

        return view('role.index', [
            'pageTitle' => 'Roles',
            'columns' => $this->datatableService->getColumns($data),
        ]);
    }

    public function create()
    {
        return view('role.create', [
            'pageTitle' => 'Create a new role',
        ]);
    }

    public function edit(Role $role)
    {
        return view('role.edit', [
            'pageTitle' => 'Edit '.$role->name,
            'role' => $role,
        ]);
    }

    public function store(RoleRequest $request)
    {
        $request['guard_name'] = 'web';
        $role = $this->roleServices->storeRole($request->validated());

        $role->syncPermissions($request->permissions);

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['ip' => request()->ip()])
            ->log('Created a new role');

        return redirect()
            ->back()
            ->with('success', 'Role created successfully.');
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role = $this->roleServices->updateRole($request->validated(), $role);

        $role->syncPermissions($request->permissions);

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['ip' => request()->ip()])
            ->log('Updated the role');

        return redirect()
            ->back()
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role = $this->roleServices->destroyRole($role);

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['ip' => request()->ip()])
            ->log('Deleted the role');

        return redirect()
            ->back()
            ->with('success', 'Role deleted successfully.');
    }
}
