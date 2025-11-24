<?php

namespace App\Http\Controllers;

use App\Services\CentralClientService;
use Illuminate\Http\Request;

class CitizenAccessController extends Controller
{
    protected $centralClientService;

    public function __construct(CentralClientService $centralClientService)
    {
        $this->centralClientService = $centralClientService;
    }

    public function index(Request $request)
    {
        $uuid = $request->query('uuid');
        $citizen = null;

        if ($uuid) {
            $citizen = $this->centralClientService->fetchByUuid($uuid);
            if ($citizen) {
                // Store citizen data in session to keep them "logged in"
                session(['citizen' => $citizen]);
            } else {
                return back()->with('alertInfo', "No citizen found.");
            }
        } else {
            // Check if already in session
            $citizen = session('citizen');
        }

        return view('landing', compact('citizen'))->with('citizen');
    }
}
