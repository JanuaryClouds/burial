<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserInvitation;
use App\Services\Auth\InvitationService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvitationController extends Controller
{
    public function index(Request $request, InvitationService $service)
    {
        $service->authorizeAdmin();

        $data = ['invitations' => UserInvitation::latest('id')->get(),
            'roles' => Role::where('guard_name', 'web')->whereNotIn('name', ['staff', 'superadmin'])->orderBy('name')->pluck('name')];
        if ($request->expectsJson()) {
            return response()->json(['html' => view('user.partials.invitation-rows', $data)->render()])
                ->header('Cache-Control', 'private, no-store');
        }

        return view('user.invitations', $data);
    }

    public function store(Request $request, InvitationService $service)
    {
        $service->authorizeAdmin();
        $data = $request->validate(['email' => ['required', 'email', 'max:254'], 'roles' => ['sometimes', 'array']]);
        $invitation = $service->create($data['email'], $data['roles'] ?? []);
        $delivery = $service->send($invitation);

        return $this->result($request, $delivery->status === 'sent'
            ? 'Invitation created. Email accepted by SMTP.' : 'Invitation created. '.$delivery->failureDescription());
    }

    public function manage(Request $request, UserInvitation $invitation, InvitationService $service)
    {
        $service->authorizeAdmin();
        $data = $request->validate(['action' => ['required', 'in:renew,roles,revoke,resend'], 'roles' => ['sometimes', 'array']]);
        if ($data['action'] === 'resend') {
            $delivery = $service->send($invitation);

            return $this->result($request, $delivery->status === 'sent' ? 'Email accepted by SMTP.' : $delivery->failureDescription());
        }
        $service->manage($invitation, $data['action'], $data['roles'] ?? []);

        return $this->result($request, $data['action'] === 'renew'
            ? 'Invitation renewed for 24 hours. No email was sent.' : 'Invitation updated.');
    }

    public function form(string $token, InvitationService $service)
    {
        try {
            $invitation = $service->findUsable($token);
        } catch (HttpException $exception) {
            if ($exception->getStatusCode() !== 410) {
                throw $exception;
            }

            return response()->view('auth.invitation-unavailable', [], 410);
        }

        return view('auth.accept-invitation', ['invitation' => $invitation, 'token' => $token]);
    }

    public function accept(Request $request, string $token, InvitationService $service)
    {
        $service->accept($token, $request->all());
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->expectsJson()
            ? response()->json(['message' => 'Your account is ready. Please sign in.', 'redirect' => route('admin.login')])
            : redirect()->route('admin.login')->with('success', 'Your account is ready. Please sign in.');
    }

    private function result(Request $request, string $message)
    {
        $request->session()->flash('success', $message);

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'redirect' => route('user.invitations.index')])
            : redirect()->route('user.invitations.index')->with('success', $message);
    }
}
