<?php

namespace App\Livewire\ActivityLog;

use App\Models\User;
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
        $this->logs = Activity::with(['causer', 'subject'])
            ->select('id', 'log_name', 'description', 'causer_type', 'causer_id', 'subject_type', 'subject_id', 'properties', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->mappedLogs = $this->logs
            ->map(function (Activity $log) {
                $source = $log->causer instanceof User
                    ? $log->causer->fullname().' (#'.$log->causer_id.')'
                    : ($log->causer ? $log->causer->getAttribute('first_name') : ($log->causer_id ? 'Account #'.$log->causer_id.' (unavailable)' : 'Not recorded'));
                if (! $log->causer_id && $log->log_name === 'account-management'
                    && $log->subject_type === (new User)->getMorphClass() && $log->subject_id) {
                    $account = $log->subject instanceof User ? $log->subject->fullname().' (#'.$log->subject_id.')' : '#'.$log->subject_id;
                    $source = 'Affected account: '.$account.'; actor not recorded';
                }

                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'source' => $source,
                    'logged_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'show_route' => route('system.logs.show', $log->id),
                ];
            });

        $this->columns = collect([
            ['data' => 'description'],
            ['data' => 'source'],
            ['data' => 'logged_at'],
            ['data' => 'show_route'],
        ]);

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
