<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Cancellation;
use App\Models\Closure;
use App\Models\Referral;
use App\Models\Rejection;
use App\Services\Auth\AccountAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ApplicationProcessingService
{
    /** Must be called inside the writer's transaction. */
    public function lock(string $uuid, ?string $expected): Application
    {
        app(AccountAccess::class)->assertWritable();
        $application = Application::whereKey($uuid)->lockForUpdate()->firstOrFail();
        $application->assertAssignment($expected);
        abort_if($application->isFormerClaimant(auth()->id()), 403);
        $application->load(['client', 'assessment', 'rejection', 'cancellation', 'referral', 'closure', 'activeRecommendation.workflow', 'workflowStage']);

        return $application;
    }

    public function outcome(string $uuid, ?string $expected, string $action, array $data): void
    {
        DB::transaction(function () use ($uuid, $expected, $action, $data) {
            $application = $this->lock($uuid, $expected);
            Gate::authorize($action, $application);
            $model = match ($action) {
                'cancel' => Cancellation::class,
                'reject' => Rejection::class,
                'refer' => Referral::class,
                'close' => Closure::class,
            };
            $attributes = ['application_uuid' => $uuid, 'reason' => $data['reason']];
            $attributes += match ($action) {
                'cancel' => ['cancelled_by' => auth()->id()],
                'reject' => ['rejected_by' => auth()->id()],
                'refer' => ['referred_to' => $data['referred_to']],
                'close' => ['closed_by' => auth()->id(), 'closed_at' => now()],
            };
            $model::create($attributes);
            if ($action !== 'close' && $application->activeRecommendation) {
                $application->activeRecommendation->update(['status' => match ($action) {
                    'cancel' => 'cancelled', 'reject' => 'rejected', 'refer' => 'referred',
                }]);
            }
            ActivityLoggerService::logSuccess('Application outcome: '.$action, [
                'application_uuid' => $uuid, 'claimant_assignment_uuid' => $expected,
            ]);
        }, 3);
    }
}
