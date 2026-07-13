<?php

namespace App\Services;

use App\Models\Application;
use App\Models\WorkflowStep;

class ApplicationService
{
    public function index(string $orderBy = 'tracking_no', string $orderDirection = 'asc')
    {
        return Application::with([
            'client',
            'client.interviews',
            'assessment',
            'recommendations',
            'recommendations.assistance'
        ])
            ->orderBy($orderBy, $orderDirection)
            ->get()
            ->map(function ($application) {
                $status = $application->status();

                return [
                    'uuid' => $application->uuid,
                    'tracking_no' => $application->tracking_no,
                    'client' => $application->client->fullname(),
                    'beneficiary' => $application->beneficiary->fullname(),
                    'status' => $status,
                    'application_date' => $application->created_at->format('F d, Y'),
                    'show_route' => route('application.show', $application),
                ];
            });
    }
}