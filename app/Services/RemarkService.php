<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Recommendation;
use App\Models\Remark;
use App\Models\WorkflowHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RemarkService
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        //
    }

    /**
     * Summary of store
     */
    public function store(array $data, Model $model): Remark
    {
        abort_unless($this->canRead($model) && (auth()->user()->hasOfficeAccess() || $model instanceof WorkflowHistory), 403);
        $run = $model instanceof Recommendation ? $model
            : ($model instanceof WorkflowHistory ? $model->recommendation()->firstOrFail() : null);
        abort_unless($run !== null, 403);

        return DB::transaction(function () use ($data, $model, $run) {
            $application = app(ApplicationProcessingService::class)->lock($run->application_uuid, $run->claimant_assignment_uuid);
            abort_unless($this->canWrite($model), 403);
            abort_unless($application->isActive(), 403);

            return Remark::create([
                'remarkable_id' => $model->getKey(),
                'remarkable_type' => get_class($model),
                'content' => $data['content'],
                'user_id' => Auth::id(),
            ]);
        }, 3);
    }

    public function canWrite(?Model $model): bool
    {
        if (! $this->canRead($model)) {
            return false;
        }
        $run = $model instanceof Recommendation ? $model
            : ($model instanceof WorkflowHistory ? $model->recommendation()->first() : null);
        if (! $run) {
            return false;
        }
        $application = Application::find($run->application_uuid);

        return $application && (auth()->user()->hasOfficeAccess() || $application->current_recommendation_uuid === $run->uuid)
            && $application->current_claimant_assignment_uuid === $run->claimant_assignment_uuid && $application->isActive()
            && (auth()->user()->hasOfficeAccess() || $model instanceof WorkflowHistory);
    }

    public function canRead(?Model $model): bool
    {
        $user = auth()->user();
        if (! $user || ! $user->isInternal()) {
            return false;
        }
        $run = $model instanceof Recommendation ? $model
            : ($model instanceof WorkflowHistory ? $model->recommendation()->first() : null);
        $application = $run ? Application::find($run->application_uuid) : null;

        return $application && app(ApplicationAccessService::class)->view($user, $application)
            && ($user->hasOfficeAccess() || $application->current_recommendation_uuid === $run->uuid);
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
