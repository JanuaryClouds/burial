<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WorkflowVersionAudit
{
    /** Read-only mapping; stage evidence always takes precedence over mutable classification suggestions. */
    public function inspect(): array
    {
        $hasGroups = Schema::hasColumn('workflows', 'workflow_group_uuid');
        $hasCurrentPointer = Schema::hasColumn('applications', 'current_recommendation_uuid');
        $hasPins = Schema::hasColumn('recommendations', 'workflow_uuid');
        $stages = DB::table('workflow_stages')->get(['uuid', 'workflow_uuid', 'position', 'deleted_at'])->keyBy('uuid');
        $workflows = DB::table('workflows')->get(array_merge(['uuid', 'name', 'deleted_at'], $hasGroups ? ['workflow_group_uuid'] : []))->keyBy('uuid');
        $types = DB::table('funeral_assistance_types')->get(['uuid', 'workflow_uuid'])->keyBy('uuid');
        $recommendations = DB::table('recommendations')->orderBy('created_at')->get(array_merge(
            ['uuid', 'application_uuid', 'funeral_assistance_type_uuid', 'created_at'], $hasPins ? ['workflow_uuid'] : []
        ));
        $applications = DB::table('applications')->get(array_merge(
            ['uuid', 'current_workflow_stage_uuid'], $hasCurrentPointer ? ['current_recommendation_uuid'] : []
        ))->keyBy('uuid');
        $errors = [];
        $nameOwners = [];
        foreach ($workflows as $workflow) {
            $key = mb_strtolower(trim($workflow->name));
            $group = $workflow->workflow_group_uuid ?? $workflow->uuid;
            if (isset($nameOwners[$key]) && $nameOwners[$key] !== $group) {
                $errors[] = "Workflow {$workflow->uuid}: name belongs to multiple workflow groups.";
            }
            $nameOwners[$key] = $group;
        }
        // Honor the target database's collation as well as Unicode case folding.
        $groupColumn = $hasGroups ? 'workflow_group_uuid' : 'uuid';
        $collisions = DB::table('workflows')->selectRaw('LOWER(TRIM(name)) as name_key')
            ->groupByRaw('LOWER(TRIM(name))')->havingRaw('COUNT(DISTINCT '.$groupColumn.') > 1')->get();
        foreach ($collisions as $collision) {
            $errors[] = "Workflow name {$collision->name_key}: conflicting ownership under database collation.";
        }
        $historyEvidence = [];
        foreach (DB::table('workflow_histories')->select('uuid', 'recommendation_uuid', 'from_stage_uuid', 'to_stage_uuid')->lazyById(1000, 'uuid') as $history) {
            foreach ([$history->from_stage_uuid, $history->to_stage_uuid] as $uuid) {
                if ($uuid && ! isset($stages[$uuid])) {
                    $errors[] = "Recommendation {$history->recommendation_uuid}: missing historical stage {$uuid}.";
                } elseif ($uuid) {
                    $historyEvidence[$history->recommendation_uuid][$stages[$uuid]->workflow_uuid] = true;
                }
            }
        }
        $runsByApplication = $recommendations->groupBy('application_uuid');
        $current = [];
        foreach ($applications as $application) {
            $runs = $runsByApplication->get($application->uuid, collect());
            if ($hasCurrentPointer && $application->current_recommendation_uuid) {
                $run = $runs->firstWhere('uuid', $application->current_recommendation_uuid);
                if (! $run) {
                    $errors[] = "Application {$application->uuid}: invalid current recommendation.";
                }
            } else {
                $candidates = $runs->where('created_at', $runs->max('created_at'));
                if ($candidates->count() > 1) {
                    $errors[] = "Application {$application->uuid}: tied latest recommendation timestamps.";
                }
                $run = $candidates->first();
            }
            $current[$application->uuid] = $run?->uuid;
            if ($application->current_workflow_stage_uuid && ! $run) {
                $errors[] = "Application {$application->uuid}: stage without a recommendation.";
            }
        }
        $pins = [];
        $inferred = [];
        foreach ($recommendations as $run) {
            $evidence = array_keys($historyEvidence[$run->uuid] ?? []);
            $application = $applications->get($run->application_uuid);
            if (! $application) {
                $errors[] = "Recommendation {$run->uuid}: missing application.";
            }
            if (($current[$run->application_uuid] ?? null) === $run->uuid && $application?->current_workflow_stage_uuid) {
                $stage = $stages->get($application->current_workflow_stage_uuid);
                if (! $stage || $stage->deleted_at || ! $stage->position) {
                    $errors[] = "Application {$application->uuid}: missing, retired, or unpositioned current stage.";
                }
                if ($stage) {
                    $evidence[] = $stage->workflow_uuid;
                }
            }
            if (isset($run->workflow_uuid)) {
                $evidence[] = $run->workflow_uuid;
            }
            $evidence = array_values(array_unique($evidence));
            if (count($evidence) > 1) {
                $errors[] = "Recommendation {$run->uuid}: contradictory workflow evidence.";
            }
            $pin = $evidence[0] ?? ($types[$run->funeral_assistance_type_uuid]->workflow_uuid ?? null);
            if ($pin && (! isset($workflows[$pin]) || $workflows[$pin]->deleted_at)) {
                $errors[] = "Recommendation {$run->uuid}: unavailable legacy workflow {$pin}.";
            }
            if (! $evidence && $pin) {
                $inferred[] = $run->uuid;
            }
            $pins[$run->uuid] = $pin;
        }

        return ['errors' => array_values(array_unique($errors)), 'recommendations' => $pins,
            'applications' => $current, 'inferred' => $inferred,
            'unassigned' => array_keys(array_filter($pins, fn ($pin) => $pin === null))];
    }
}
