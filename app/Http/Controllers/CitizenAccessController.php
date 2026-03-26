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

        $uuid = $request->query('uuid');

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

            return redirect()->route('landing.page');
        }

        return view('landing', compact(
            'steps',
            'burialDocuments',
            'funeralDocuments',
            'page_title',
        ));
    }
}
