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
        abort_unless(auth()->user()?->hasAnyRole(['staff', 'superadmin']), 403);
        $run = $model instanceof Recommendation ? $model
            : ($model instanceof WorkflowHistory ? $model->recommendation()->firstOrFail() : null);
        abort_unless($run, 403);

        return DB::transaction(function () use ($data, $model, $run) {
            $application = app(ApplicationProcessingService::class)->lock($run->application_uuid, $run->claimant_assignment_uuid);
            abort_unless($application->isActive(), 403);

            return Remark::create([
                'remarkable_id' => $model->uuid,
                'remarkable_type' => get_class($model),
                'content' => $data['content'],
                'user_id' => Auth::id(),
            ]);
        }, 3);
    }

    public function canWrite(?Model $model): bool
    {
        if (! auth()->user()?->hasAnyRole(['staff', 'superadmin'])) {
            return false;
        }
        $run = $model instanceof Recommendation ? $model
            : ($model instanceof WorkflowHistory ? $model->recommendation()->first() : null);
        if (! $run) {
            return false;
        }
        $application = Application::find($run->application_uuid);

        return $application && $application->current_claimant_assignment_uuid === $run->claimant_assignment_uuid && $application->isActive();
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
