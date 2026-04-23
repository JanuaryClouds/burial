<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\Auth\UserService;
use App\Services\DatatableService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userServices;

    protected $datatableServices;

    public function __construct(UserService $userServices, DatatableService $datatableService)
    {
        $this->userServices = $userServices;
        $this->datatableServices = $datatableService;
    }

    public function login(LoginRequest $request)
    {
        try {
            $ip = request()->ip();
            $browser = request()->header('User-Agent');

            if (! Auth::attempt($request->validated())) {
                return back()
                    ->withErrors(['error' => 'Invalid username or password.'])
                    ->withInput();
            }

            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                activity()
                    ->causedBy($user)
                    ->withProperties(['ip' => $ip, 'browser' => $browser])
                    ->log('Inactive account login attempt');

                return redirect()->back()->with('warning', 'Your account is inactive. Please contact the superadmin.');
            }

            if ($user->roles()->count() == 0) {
                activity()
                    ->causedBy($user)
                    ->withProperties(['ip' => $ip, 'browser' => $browser])
                    ->log('Client attempted to access admin panel');

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('landing.page')->with('warning', 'You do not have permission to access this page');
            }

            $token = $user->createToken('fileserver')->plainTextToken;
            session(['api_token' => $token]);

            return redirect()
                ->route('dashboard');
        } catch (Exception $e) {
            activity()
                ->causedBy(null)
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Unsuccessful login attempt');

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function checkSession()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $accessLevel = $user->roles()->exists() ? 'Staff' : 'Client';
            $redirect = $user->clients()->exists() ? route('dashboard') : route('general.intake.form');

            return response()->json([
                'success' => true,
                'access_level' => $accessLevel,
                'message' => 'Session validated',
                'redirect' => $redirect,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Session not validated',
        ], 401);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->log('Successful logout');

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        if (session()->has('citizen')) {
            session()->forget('citizen');
        }

        return redirect()
            ->route('landing.page');
    }

    public function loginPage()
    {
        $user = auth()->user();

        if ($user) {
            if ($user->roles->count() === 0) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return redirect()->route('landing.page');
            } else {
                return redirect()->route('dashboard');
            }
        }

        return view('login');
    }

    public function index()
    {
        $this->authorize('view-users');
        $page_title = 'Users';
        $resource = 'user';
        $data = $this->userServices->index();

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        $columns = $this->datatableServices->getColumns($data, ['is_active']);

        return view('cms.index', compact('data', 'page_title', 'resource', 'columns'));
    }

    public function edit(User $user)
    {
        $this->authorize('edit-users');
        $page_title = 'Edit User';
        $resource = 'user';
        $roles = Role::where('name', '!=', 'superadmin')->get();
        $data = User::select('id', 'first_name', 'middle_name', 'last_name', 'email', 'contact_number', 'is_active')->find($user->id);

        return view('cms.edit', compact('data', 'page_title', 'resource', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('edit-users');
        try {
            $user = User::find($user->id);
            $user = $this->userServices->update($request->validated(), $user);

            return redirect()->route('user.edit', $user)->with('success', 'User updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function store(CreateUserRequest $request)
    {
        $this->authorize('create-users');
        try {
            $user = $this->userServices->storeUser($request->validated());

            return redirect()->route('user.edit', $user)->with('success', 'User created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
