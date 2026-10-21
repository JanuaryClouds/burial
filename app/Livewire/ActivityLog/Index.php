<?php

namespace App\Livewire\ActivityLog;

use App\Services\DataTableService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    public Collection $logs;

    public Collection $mappedLogs;

    public Collection $columns;

    public string $dataTableId = 'activity-logs-table';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->logs = Activity::with('causer')
            ->select('id', 'description', 'causer_type', 'causer_id', 'properties', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->mappedLogs = $this->logs
            ->map(function ($log) {
                $properties = $log->properties;

                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'source' => $log->causer->first_name ?? '',
                    'logged_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'show_route' => route('system.logs.show', $log->id),
                ];
            });

        $this->columns = app(DataTableService::class)
            ->getColumns($this->mappedLogs, ['id']);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => 'activity-logs-table',
            'rows' => $this->mappedLogs,
        ]);
    }

    public function render()
    {
        return view('livewire.activity-log.index');
    }
}
