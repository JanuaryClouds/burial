<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequirement;
use App\Models\FuneralAssistanceType;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Auth\AccountAccess;
use App\Services\CentralClientService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CitizenAccessController extends Controller
{
    public function __construct(
        protected CentralClientService $centralClientService,
        protected ImageService $imageService
    ) {}

    public function index(Request $request)
    {
        if (app()->isProduction() && SystemSetting::first()->maintenance_mode) {
            return response()->view('error.maintenance', [], 503);
        }

        if (Auth::user()) {
            return redirect()->route('dashboard');
        }

        $pageTitle = 'Funeral Assistance System - Taguig CSWDO';

        $services = FuneralAssistanceType::select(['name', 'description'])
            ->get();

        $documents = DocumentRequirement::index()
            ->where('is_active', true)
            ->get();

        if (config('services.portal.users.mock')) {
            $citizens = User::orderBy('created_at')
                ->whereDoesntHave('roles')
                ->get();

            $testLinks = [];

            if (config('services.portal.users.enable.get')) {
                foreach ($citizens as $citizen) {
                    $payload = [
                        'citizen_uuid' => $citizen->citizen_uuid,
                        'nonce' => Str::random(32),
                    ];

                    $encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
                    $signature = hash_hmac('sha256', $encoded, config('services.portal.sso.secret'));

                    $url = url('/sso/callback')."?payload={$encoded}&signature={$signature}";

                    $testLinks[] = [
                        'label' => $citizen->fullname(),
                        'url' => $url,
                    ];
                }
            } else {
                foreach ($citizens as $citizen) {
                    $url = url('/sso/callback')."?citizen_uuid={$citizen->citizen_uuid}";
                    $testLinks[] = [
                        'label' => $citizen->fullname(),
                        'url' => $url,
                    ];
                }
            }

            return view('test.zero', [
                'testUsers' => $testLinks,
            ]);
        }

        return view('landing', compact(
            'services',
            'documents',
            'pageTitle',
        ));
    }

    public function sso()
    {
        abort_if(auth()->user()?->isInternal(), 403, 'Staff accounts must use the staff login.');
        if (config('services.portal.users.enable.get')) {
            $ssoRequest = request('payload');
            $signature = request('signature');

            if (! is_string($ssoRequest) || ! is_string($signature) || $ssoRequest === '' || $signature === '') {
                activity()
                    ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                    ->log('Missing SSO parameters.');

                return redirect()->back()->with('error', 'Login failed.');
            }

            $secret = config('services.portal.sso.secret');
            if (empty($secret)) {
                activity()
                    ->log('SSO secret is not set.');
                abort(500, 'Login failed.');
            }

            $expectedSignature = hash_hmac('sha256', $ssoRequest, config('services.portal.sso.secret'));

            if (! hash_equals($expectedSignature, $signature)) {
                activity()
                    ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                    ->log('Invalid SSO signature.');

                return redirect()->back()->with('error', 'Login Failed.');
            }

            $decoded = base64_decode(strtr($ssoRequest, '-_', '+/'), true);
            $payload = $decoded === false ? null : json_decode($decoded, true);

            if (! is_array($payload) || ! is_string($payload['citizen_uuid'] ?? null) || $payload['citizen_uuid'] === '') {
                activity()
                    ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                    ->log('Incomplete SSO payload.');

                return redirect()->back()->with('error', 'Login failed.');
            }

            $uuid = $payload['citizen_uuid'];

            $user = $this->centralClientService->checkIfUser('uuid', $uuid, false);

            if ($user && ! $user->hasRole('superadmin') && SystemSetting::first()->maintenance_mode) {
                return response()->view('error.maintenance', [], 503);
            }

            if ($user === null) {
                return redirect()->route('landing.page')->with('error', 'User not found.');
            }

        } else {
            $user = User::where('citizen_uuid', request('citizen_uuid'))->firstOrFail();
        }

        abort_unless(app(AccountAccess::class)->isCitizen($user), 403, 'Staff accounts must use the staff login.');
        abort_if(Auth::check() && Auth::id() !== $user->id, 403, 'Sign out before switching accounts.');

        if (! Auth::check()) {
            Auth::login($user);
            if ($user->is_active) {
                $user->createToken('fileserver');
            }
        }

        $redirect = ! $user->is_active || $user->clients()->exists() ? route('dashboard') : route('client.create');

        return redirect()->to($redirect);
    }

    public function logout()
    {
        $payload = [
            'citizen_uuid' => auth()->user()->citizen_uuid,
            'nonce' => Str::random(32),
        ];

        $encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', $encoded, config('services.portal.sso.secret'));

        $user = Auth::user();
        $user->tokens()->delete();
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        if (session()->has('citizen')) {
            session()->forget('citizen');
        }

        $endpoint = config('services.portal.endpoint');

        if (empty($endpoint) || app()->isLocal()) {
            return redirect()->route('landing.page');
        }

        return redirect($endpoint);
    }
}
