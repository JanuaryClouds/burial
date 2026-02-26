<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStep;
use App\Models\BurialAssistance;
use App\Models\Client;
use App\Models\DocumentRequirement;
use App\Models\FuneralAssistance;
use App\Services\CentralClientService;
use App\Services\ImageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitizenAccessController extends Controller
{
    protected $centralClientService;
    protected $imageService;

    public function __construct(
        CentralClientService $centralClientService,
        ImageService $imageService
    )   {
        $this->centralClientService = $centralClientService;
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $page_title = 'Funeral Assistance System - Taguig CSWDO';
        $steps = ApplicationStep::steps();
        $burialDocuments = DocumentRequirement::burial();
        $funeralDocuments = DocumentRequirement::funeral();

        $uuid = $request->query('uuid');
        if ($uuid) {
            $user = $this->centralClientService->checkIfUser($uuid);
        }

        if (isset($user)) {
            if (! $user->is_active) 
                return redirect()->route('landing.page')->with('info', 'Your account has been deactivated. Please contact us to activate your account.');
            Auth::login($user);
            $token = $user->createToken('fileserver')->plainTextToken;
            return redirect()->route('landing.page');
        } else {
            return view('landing', compact(
                'steps',
                'burialDocuments',
                'funeralDocuments',
                'page_title',
            ));
        }
    }
}
