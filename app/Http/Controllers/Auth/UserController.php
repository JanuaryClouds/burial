<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\Auth\LoginService;
use App\Services\Auth\UserService;
use App\Services\DataTableService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userServices,
        protected DataTableService $dataTableServices,
    ) {}

    public function login(LoginRequest $request)
    {
        $result = app(LoginService::class)->attempt($request->validated());
        if (isset($result['redirect'])) {
            return redirect()->to($result['redirect']);
        }

        return back()->withErrors(['error' => $result['message']])
            ->withInput($request->only('email'))
            ->with('login_recovery', $result['failures'] >= 3);
    }

    public function checkSession()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $accessLevel = $user->roles()->exists() ? 'Staff' : 'Client';
            $redirect = route('dashboard');

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
        $user?->tokens()->delete();

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

                return redirect(config('services.portal.endpoint') ?: route('landing.page'));
            } else {
                return redirect()->route('dashboard');
            }
        }

        return view('login');
    }

    public function index()
    {
        return view('user.index', [
            'pageTitle' => 'Users',
        ]);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return view('user.show', [
            'pageTitle' => $user->fullname(),
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('user.edit', [
            'pageTitle' => 'Edit '.$user->first_name,
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        try {
            $user = User::find($user->id);
            $data = $request->validated();
            $roles = array_key_exists('roles', $data)
                ? Role::whereIn('id', $data['roles'] ?? [])->pluck('name')
                : $user->roles()->whereNotIn('name', ['staff', 'superadmin'])->pluck('name');
            $user = $this->userServices->update([
                'firstName' => $data['first_name'], 'middleName' => $data['middle_name'] ?? null,
                'lastName' => $data['last_name'], 'suffix' => $data['suffix'] ?? null,
                'email' => $user->email, 'contactNumber' => $data['contact_number'],
                'isActive' => $data['is_active'] ?? $user->is_active,
                'roles' => $roles->mapWithKeys(fn ($role) => [$role => true])->all(),
            ], $user);

            return redirect()->route('user.edit', $user)->with('success', 'User updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->userServices->store([
                'firstName' => $data['first_name'], 'middleName' => $data['middle_name'] ?? null,
                'lastName' => $data['last_name'], 'suffix' => $data['suffix'] ?? null,
                'email' => $data['email'], 'contactNumber' => $data['contact_number'],
                'password' => $data['password'], 'confirmPassword' => $request->input('password_confirmation'),
                'roles' => [],
            ]);

            return redirect()->route('user.edit', $user)->with('success', 'User created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
