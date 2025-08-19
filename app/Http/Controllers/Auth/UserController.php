<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\UserService;
use App\Http\Requests\Auth\LoginRequest;
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
        $ip = request()->ip();
        $browser = request()->header('User-Agent');
        
        if ($user = $this->userServices->login($request->validated()))
        {
            Auth::login($user);
            
            activity() 
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log("Successful login");
                
            return redirect()
                ->route(Auth::user()->getRoleNames()->first() . '.dashboard')
                ->with('success', 'You have successfully logged in!');
        } else {
            activity()
                ->log("Failed login attempt: ({$ip} - {$browser})");
            
            return redirect()
                ->route('login.page')
                ->with('error', 'Invalid login credentials.');
        }
    }

    public function logout()
    {
        $user = Auth::user();
        
        activity()
        ->performedOn($user)
        ->causedBy($user)
        ->log('Successful logout');
        
        $user = $this->userServices->logout();
        
        return redirect()
            ->route('landing.page')
            ->with('success','You have successfully logged out!');
    }

    public function loginPage()
    {
        return view('auth.login');
    }
}