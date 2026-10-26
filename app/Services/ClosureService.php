<?php

namespace App\Services;

use App\Models\Application;

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
        app(ApplicationProcessingService::class)->outcome(
            $application->uuid, $application->current_claimant_assignment_uuid, 'close', $data
        );
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
