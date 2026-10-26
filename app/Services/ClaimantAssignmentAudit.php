<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClaimantAssignmentAudit
{
    /** Read only; safe to run before the assignment migration. */
    public function inspect(): array
    {
        $errors = [];
        foreach (['applications', 'clients', 'users', 'interviews', 'assessments', 'recommendations', 'cheques'] as $table) {
            if (! Schema::hasTable($table)) {
                $errors[] = "Missing required table: {$table}.";
            }
        }
        if ($errors) {
            return $errors;
        }
        foreach (['uuid', 'application_uuid', 'status', 'date_claimed'] as $column) {
            if (! Schema::hasColumn('cheques', $column)) {
                $errors[] = "Incompatible cheques schema: missing {$column}.";
            }
        }
        foreach (DB::table('applications')->select('client_uuid')->groupBy('client_uuid')->havingRaw('COUNT(*) > 1')->pluck('client_uuid') as $uuid) {
            $errors[] = "Client {$uuid} belongs to multiple applications; interview backfill is ambiguous.";
        }
        $assessmentKey = Schema::hasColumn('assessments', 'claimant_assignment_uuid') ? 'claimant_assignment_uuid' : 'application_uuid';
        foreach (DB::table('assessments')->select($assessmentKey)->groupBy($assessmentKey)->havingRaw('COUNT(*) > 1')->pluck($assessmentKey) as $uuid) {
            $errors[] = "Application/assignment {$uuid} has multiple assessments; resolve before backfill.";
        }
        if (DB::table('applications as a')->leftJoin('clients as c', 'c.uuid', '=', 'a.client_uuid')->leftJoin('users as u', 'u.id', '=', 'c.user_id')->whereNull('u.id')->exists()) {
            $errors[] = 'An application has no valid client account.';
        }
        foreach (['assessments', 'recommendations'] as $table) {
            if (DB::table("{$table} as p")->leftJoin('applications as a', 'a.uuid', '=', 'p.application_uuid')->whereNull('a.uuid')->exists()) {
                $errors[] = "Orphaned {$table} records exist.";
            }
        }
        if (! Schema::hasColumn('applications', 'current_recommendation_uuid')) {
            $errors[] = 'Apply workflow versioning before claimant assignments.';
        } else {
            if (DB::table('applications as a')->leftJoin('recommendations as r', 'r.uuid', '=', 'a.current_recommendation_uuid')
                ->whereNotNull('a.current_recommendation_uuid')->where(fn ($q) => $q->whereNull('r.uuid')->orWhereColumn('r.application_uuid', '!=', 'a.uuid'))->exists()) {
                $errors[] = 'An application points to an invalid recommendation.';
            }
            if (DB::table('applications as a')->leftJoin('recommendations as r', 'r.uuid', '=', 'a.current_recommendation_uuid')
                ->leftJoin('workflow_stages as s', 's.uuid', '=', 'a.current_workflow_stage_uuid')->whereNotNull('a.current_workflow_stage_uuid')
                ->where(fn ($q) => $q->whereNull('r.workflow_uuid')->orWhereNull('s.uuid')->orWhereColumn('s.workflow_uuid', '!=', 'r.workflow_uuid'))->exists()) {
                $errors[] = 'An application stage does not belong to its current recommendation workflow.';
            }
        }

        if (Schema::hasTable('application_claimant_assignments')) {
            if (DB::table('applications as a')->leftJoin('application_claimant_assignments as c', 'c.uuid', '=', 'a.current_claimant_assignment_uuid')
                ->where(fn ($q) => $q->whereNull('c.uuid')->orWhereNotNull('c.ended_at')->orWhereColumn('c.application_uuid', '!=', 'a.uuid')->orWhereColumn('c.client_uuid', '!=', 'a.client_uuid'))->exists()) {
                $errors[] = 'An application has an inconsistent current claimant assignment.';
            }
            foreach (['assessments', 'recommendations'] as $table) {
                if (DB::table($table.' as p')->leftJoin('application_claimant_assignments as c', 'c.uuid', '=', 'p.claimant_assignment_uuid')
                    ->where(fn ($q) => $q->whereNull('c.uuid')->orWhereColumn('c.application_uuid', '!=', 'p.application_uuid'))->exists()) {
                    $errors[] = "A {$table} record has an inconsistent claimant assignment.";
                }
            }
            if (DB::table('applications as a')->join('recommendations as r', 'r.uuid', '=', 'a.current_recommendation_uuid')
                ->whereColumn('r.claimant_assignment_uuid', '!=', 'a.current_claimant_assignment_uuid')->exists()) {
                $errors[] = 'A current recommendation belongs to a previous claimant assignment.';
            }
        }

        return $errors;
    }
}
