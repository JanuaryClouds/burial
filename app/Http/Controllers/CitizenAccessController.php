<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStep;
use App\Models\DocumentRequirement;
use App\Models\SystemSetting;
use App\Services\CentralClientService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (app()->isLocal()) {
            return view('test.zero');
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

            if (Auth::user()->clients()->count() == 0) {
                return redirect()->route('general.intake.form');
            } else {
                return redirect()->route('dashboard');
            }
        } else {
            abort(403);
        }
    }
}
