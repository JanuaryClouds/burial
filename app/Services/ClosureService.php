<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Closure;
use Illuminate\Support\Facades\Auth;

class ClosureService
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        //
    }

    public function store(array $data, Application $application)
    {
        Closure::create([
            'application_uuid' => $application->uuid,
            'reason' => $data['reason'],
            'closed_by' => Auth::id(),
            'closed_at' => now()
        ]);
    }

    public function edit()
    {
        //
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}