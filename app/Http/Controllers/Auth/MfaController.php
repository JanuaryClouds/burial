<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AccountAccess;
use App\Services\Auth\SuperadminMfa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MfaController extends Controller
{
    public function show(SuperadminMfa $mfa)
    {
        try {
            $user = $mfa->pendingUser();
        } catch (ValidationException) {
            return redirect()->route('admin.login')->with('warning', 'Please sign in to verify your authenticator.');
        }
        $credential = $mfa->credential($user);
        $stage = $user->password_change_required ? 'password'
            : (session('mfa.pending.ack_required') ? 'codes' : ($credential->confirmed_at ? 'challenge' : 'enroll'));
        $qr = $secret = null;
        $codes = [];
        if ($stage === 'enroll') {
            $secret = $mfa->enrollmentSecret();
            $otp = SuperadminMfa::otp($secret);
            $otp->setLabel($user->email);
            $otp->setIssuer(config('app.name').' ('.app()->environment().')');
            $qr = QrCode::format('svg')->size(220)->generate($otp->getProvisioningUri());
        }
        if ($stage === 'codes' && ($encrypted = session()->pull('mfa.pending.codes'))) {
            $codes = json_decode(Crypt::decryptString($encrypted), true);
        }

        return response()->view('auth.mfa', compact('stage', 'qr', 'secret', 'codes'));
    }

    public function submit(Request $request, SuperadminMfa $mfa)
    {
        $mfa->pendingUser();
        $data = $request->validate(['action' => 'required|in:password,enroll,challenge,recover,ack',
            'mfa_code' => 'sometimes|nullable|string|max:128', 'password' => 'sometimes|string|max:256',
            'password_confirmation' => 'sometimes|string|max:256']);
        switch ($data['action']) {
            case 'password':
                $mfa->changeInitialPassword($data);
                break;
            case 'enroll':
                $mfa->enroll($data['mfa_code'] ?? '');
                break;
            case 'challenge':
                abort_if(session('mfa.pending.ack_required'), 403);
                $mfa->challenge($data['mfa_code'] ?? '');
                $mfa->finish();

                return redirect()->route('dashboard');
            case 'recover':
                $mfa->recover($data['mfa_code'] ?? '');
                break;
            case 'ack':
                abort_unless(session('mfa.pending.ack_required') && ! session()->has('mfa.pending.codes'), 403);
                $request->validate(['saved_codes' => 'accepted']);
                session(['mfa.pending.ack_required' => false]);
                $mfa->finish();

                return redirect()->route('dashboard');
        }

        return redirect()->route('mfa.show');
    }

    public function manage(Request $request, SuperadminMfa $mfa)
    {
        $user = app(AccountAccess::class)->assertWritable();
        abort_unless($user->hasRole('superadmin'), 403);
        if ($request->isMethod('post')) {
            $data = $request->validate(['password' => 'required|string|max:256', 'mfa_code' => 'required|string|max:128',
                'action' => 'required|in:replace,regenerate']);
            $mfa->manage($user, $data['password'], $data['mfa_code'], $data['action'] === 'replace');

            return redirect()->route('mfa.show');
        }

        return view('auth.mfa-manage');
    }
}
