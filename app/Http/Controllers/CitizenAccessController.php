<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStep;
use App\Models\DocumentRequirement;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\CentralClientService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CitizenAccessController extends Controller
{
    protected $centralClientService;

    protected $imageService;

    public function __construct(
        CentralClientService $centralClientService,
        ImageService $imageService
    ) {
        $this->centralClientService = $centralClientService;
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        if (app()->isProduction() && SystemSetting::first()?->maintenance_mode ?? false) {
            return response()->view('error.maintenance', [], 503);
        }

        if (auth()->user()) {
            return redirect()->route('dashboard');
        }

        $page_title = 'Funeral Assistance System - Taguig CSWDO';
        $steps = ApplicationStep::steps();
        $burialDocuments = DocumentRequirement::burial();
        $funeralDocuments = DocumentRequirement::funeral();

        if (config('services.portal.users.mock')) {
            $citizens = User::orderBy('created_at')
                ->get();
            $testLinks = [];
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

            return view('test.zero', [
                'testUsers' => $testLinks,
            ]);
        }

        return view('landing', compact(
            'steps',
            'burialDocuments',
            'funeralDocuments',
            'page_title',
        ));
    }

    public function sso()
    {
        $ssoRequest = request('payload');
        $signature = request('signature');

        if (empty($ssoRequest) || empty($signature)) {
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

        $payload = json_decode(base64_decode($ssoRequest), true);

        if (! is_array($payload) || empty($payload['citizen_uuid'])) {
            activity()
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                ->log('Incomplete SSO payload.');

            return redirect()->back()->with('error', 'Login failed.');
        }

        $uuid = $payload['citizen_uuid'];

        $user = $this->centralClientService->checkIfUser($uuid);

        if ($user && ! $user->hasRole('superadmin') && SystemSetting::first()?->maintenance_mode) {
            return response()->view('error.maintenance', [], 503);
        }

        if ($user === null) {
            return redirect()->route('landing.page')->with('error', 'User not found.');
        }

        if (! $user->is_active) {
            return redirect()->back()->with('warning', 'Your account is inactive. Please contact the superadmin.');
        }

        if (! Auth::check()) {
            Auth::login($user);
            $token = $user->createToken('fileserver')->plainTextToken;
        }

        $redirect = auth()->user()->hasRole('staff')
            ? route('dashboard')
            : (auth()->user()->clients()->exists() ? route('dashboard') : route('general.intake.form'));

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
