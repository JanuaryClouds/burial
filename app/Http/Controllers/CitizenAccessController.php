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
use Str;

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
        if (SystemSetting::first()?->maintenance_mode ?? false) {
            return response()->view('error.maintenance', [], 503);
        }

        $page_title = 'Funeral Assistance System - Taguig CSWDO';
        $steps = ApplicationStep::steps();
        $burialDocuments = DocumentRequirement::burial();
        $funeralDocuments = DocumentRequirement::funeral();

        if (config('services.portal.users.mock')) {
            $citizens = User::whereNotNull('citizen_uuid')->get();
            $testLinks = [];
            foreach ($citizens as $citizen) {
                $payload = [
                    'citizen_uuid' => $citizen->citizen_uuid,
                    'time' => time(),
                    'nonce' => Str::random(32),
                ];

                $encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
                $signature = hash_hmac('sha256', $encoded, config('services.portal.sso.secret'));

                $url = url('/sso/callback')."?payload={$encoded}&signature={$signature}";

                $testLinks[] = [
                    'name' => $citizen->fullname(),
                    'url' => $url,
                ];
            }

            $newUser = [
                'citizen_uuid' => Str::uuid()->toString(),
                'time' => time(),
                'nonce' => Str::uuid()->toString(),
            ];

            $encoded = rtrim(strtr(base64_encode(json_encode($newUser)), '+/', '-_'), '=');
            $signature = hash_hmac('sha256', $encoded, config('services.portal.sso.secret'));

            $url = url('/sso/callback')."?payload={$encoded}&signature={$signature}";

            $testLinks[] = [
                'name' => 'New User',
                'url' => $url,
            ];

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
        // example URL sent from portal: https://funeral.taguig.gov.ph/sso/callback?payload={$payload}&signature={$signature}
        $ssoRequest = request('payload');
        $signature = request('signature');

        if (empty($ssoRequest) || empty($signature)) {
            abort(403);
        }

        $secret = config('services.portal.sso.secret');
        if (empty($secret)) {
            abort(500, 'SSO secret is not set.');
        }

        $expectedSignature = hash_hmac('sha256', $ssoRequest, config('services.portal.sso.secret'));

        if (! hash_equals($expectedSignature, $signature)) {
            abort(403);
        }

        $payload = json_decode(base64_decode($ssoRequest), true);

        if (! is_array($payload) || ! isset($payload['time'], $payload['citizen_uuid'])) {
            abort(403);
        }

        $expired = time() - $payload['time'];

        if ($expired > 300) {
            abort(403);
        }

        $uuid = $payload['citizen_uuid'];

        if ($uuid) {
            $user = $this->centralClientService->checkIfUser($uuid);
            if ($user == null || ($user != null && $user->is_active == 0)) {
                return redirect()->route('landing.page');
            }

            if (! Auth::check()) {
                Auth::login($user);
                $token = $user->createToken('fileserver')->plainTextToken;
                session(['api_token' => $token]);
            }

            $redirect = auth()->user()->clients()->exists() ? route('dashboard') : route('general.intake.form');

            return redirect()->to($redirect);
        } else {
            abort(403);
        }
    }

    public function logout()
    {
        $payload = [
            'citizen_uuid' => auth()->user()->citizen_uuid,
            'time' => time(),
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

        return redirect('/');
    }
}
