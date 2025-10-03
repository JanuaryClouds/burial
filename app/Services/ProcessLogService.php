<?php

namespace App\Services;
use App\Models\Burial;
use App\Models\ProcessLog;
use App\Models\WorkflowStep;
use Carbon\Carbon;

class ProcessLogService
{
    public function getLog($claimant, $order) {
        return $claimant->processLogs()
            ->whereHasMorph('loggable', [WorkflowStep::class], fn($q) => $q->where('order_no', $order))
            ->with('loggable')
            ->first();
    }

    public function getAvgProcessingTime($application) {
        $avgProcessingTime = $application::with(['processLogs'])
            ->get()
            ->map(function ($application) {
                $logs = $application->processLogs->sortBy('created_at')->values();
                if ($logs->count() < 2) {
                    return 0;
                }
                $diffs = [];
                for ($i = 0; $i < $logs->count() - 1; $i++) {
                    $start = Carbon::parse($logs[$i]->created_at);
                    $end = Carbon::parse($logs[$i + 1]->created_at);
                    $diffs[] = $end->diffInMinutes($start);
                }
                return collect($diffs)->avg();
            })
            ->filter()
            ->values();
        return $avgProcessingTime;
    }
}