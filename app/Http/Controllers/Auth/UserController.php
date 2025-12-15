<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\Auth\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userServices;

    public function __construct(UserService $userServices)
    {
        $this->userServices = $userServices;
    }

    public function login(LoginRequest $request)
    {
        try {
            $ip = request()->ip();
            $browser = request()->header('User-Agent');

            if (Auth::attempt($request->validated())) {
                $user = Auth::user();
                if ($user->is_active) {
                    Auth::login($user);
                    activity()
                        ->causedBy($user)
                        ->withProperties(['ip' => $ip, 'browser' => $browser])
                        ->log('Successful login attempt');

                    return redirect()
                        ->route('dashboard');
                } else {
                    return redirect()->back()->with('warning', 'Your account is inactive. Please contact the superadmin.');
                }
            } else {
                return redirect()->back()->with('error', 'Invalid username or password.');
            }
        } catch (Exception $e) {
            activity()
                ->causedBy(null)
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Unsuccessful login attempt');

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function logout()
    {
        $user = Auth::user();
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->log('Successful logout');

        Auth::logout();

        return redirect()
            ->route('landing.page');
    }

    public function loginPage()
    {
        // return view('auth.login');
        return view('login');
    }

    public function index()
    {
        $page_title = 'Users';
        $resource = 'user';
        $data = User::select('id', 'first_name', 'middle_name', 'last_name', 'email', 'password', 'contact_number', 'is_active')->get();

        return view('cms.index', compact('data', 'page_title', 'resource'));
    }

    public function edit(User $user)
    {
        $page_title = 'Edit User';
        $resource = 'user';
        $roles = Role::all();
        $data = User::select('id', 'first_name', 'middle_name', 'last_name', 'email', 'contact_number', 'is_active')->find($user->id);
        if (in_array($data->id, [1, 2, 3])) {
            return redirect()->route('user.index')->with('warning', 'You cannot edit this user.');
        }

        return view('cms.edit', compact('data', 'page_title', 'resource', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
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
