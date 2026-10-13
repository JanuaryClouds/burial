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
    public function __construct(
        protected UserService $userServices,
        protected DatatableService $datatableServices,
    ) {}

    public function login(LoginRequest $request)
    {
        $ip = request()->ip();
        $browser = request()->header('User-Agent');
        try {

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

            return redirect()->back()->with('error', 'Unable to login'.(app()->hasDebugModeEnabled() ? ': '.$e->getMessage() : ''));
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

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        if (session()->has('citizen')) {
            session()->forget('citizen');
        }

        if (config('services.portal.endpoint')) {
            return redirect(config('services.portal.endpoint'));
        }

        return redirect()->route('landing.page');
    }

    public function loginPage()
    {
        $user = auth()->user();

        if ($user) {
            if ($user->roles->count() === 0) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return redirect(config('services.portal.endpoint'));
            } else {
                return redirect()->route('dashboard');
            }
        }

        return view('login');
    }

    public function index()
    {
        $data = User::index()
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->fullname(),
                    'roles' => $user->roles()->count() > 0 ? 'Staff' : 'Client',
                    'active' => $user->is_active,
                    'email' => $user->email,
                    'contact_number' => $user->contact_number,
                    'show_route' => route('user.edit', $user),
                ];
            });

        if (request()->expectsJson()) {
            $this->datatableServices->ajax($data);
        }

        return view('user.index', [
            'pageTitle' => 'Users',
            'columns' => $this->datatableServices->getColumns($data),
        ]);
    }

    public function show(User $user)
    {
        $this->authorize('show', $user);

        return view('user.show', [
            'pageTitle' => $user->fullname(),
            'user' => $user
        ]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('user.edit', [
            'pageTitle' => 'Edit ' . $user->first_name,
            'user' => $user
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

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
        try {
            $user = $this->userServices->storeUser($request->validated());

            return redirect()->route('user.edit', $user)->with('success', 'User created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
