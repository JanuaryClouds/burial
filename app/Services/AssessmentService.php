<?php

namespace App\Services;

use App\Models\Assessment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AssessmentService
{
    public function store(array $data, string $applicationUuid): Assessment
    {
        Validator::make($data, [
            'expected_assignment_uuid' => ['required', 'uuid'],
            'problem_presented' => ['required', 'string', 'max:65535'],
            'swa' => ['required', 'string', 'max:65535'],
        ])->validate();

        return DB::transaction(function () use ($data, $applicationUuid) {
            $application = app(ApplicationProcessingService::class)->lock($applicationUuid, $data['expected_assignment_uuid']);
            Gate::authorize('create', [Assessment::class, $application]);
            $assessment = Assessment::create([
                'application_uuid' => $applicationUuid,
                'claimant_assignment_uuid' => $application->current_claimant_assignment_uuid,
                'problem_presented' => $data['problem_presented'],
                'swa' => $data['swa'],
            ]);
            ActivityLoggerService::logSuccess('Created assessment', ['assessment_uuid' => $assessment->uuid, 'claimant_assignment_uuid' => $application->current_claimant_assignment_uuid]);

            return $assessment;
        }, 3);
    }
}
