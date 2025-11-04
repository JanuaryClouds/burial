<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\UserService;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

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
                        ->log("Successful login attempt");
                    
                    return redirect()
                        ->route('dashboard')
                        ->with('success', 'You have successfully logged in!');
                } else {
                    return redirect()->back()->with('alertWarning', 'Your account is inactive. Please contact the superadmin.');
                }
            } else {
                activity()
                    ->causedBy(null)
                    ->withProperties(['ip' => $ip, 'browser' => $browser])
                    ->log("Unsuccessful login attempt");
                    
                return redirect()
                    ->back()
                    ->with('error', 'Invalid login credentials.');
            }   
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());
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
            ->route('landing.page')
            ->with('success','You have successfully logged out!');
    }

    public function loginPage()
    {
        return view('auth.login');
    }
}