<?php

namespace App\Http\Controllers;

use App\Services\CentralClientService;
use Exception;
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
        if (session()->has('citizen')) {
            // Change session citizen if the uuid is different
            if (session('citizen')['user_id'] != $uuid) {
                session(['citizen' => null]);
                $citizen = null;
            }
        }

        if (is_null(session('citizen')) && $uuid) {
            try {
                $citizen = $this->centralClientService->fetchCitizen($uuid);

                if ($citizen) {
                    // Store citizen data in session to keep them "logged in"
                    session(['citizen' => $citizen]);

                } else {
                    return back()->with('alertInfo', "No citizen found.");
                }
            } catch (Exception $e) {
                return back()->with('alertInfo', $e->getMessage());
            }
        } else {
            // Check if already in session
            $citizen = session('citizen');

        }

        return view('landing', compact('citizen'));
    }
}
