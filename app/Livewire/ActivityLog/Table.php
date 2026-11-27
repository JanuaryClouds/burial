<?php

namespace App\Livewire\ActivityLog;

use App\Models\User;
use App\Services\Auth\AuthenticationLog;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Table extends Component
{
    use HasPlaceholder;

    #[Locked]
    public string $kind = 'general';

    public Collection $mappedLogs;

    public Collection $columns;

    public function boot(): void
    {
        abort_unless(auth()->user()?->fresh(['roles'])?->isSystemAdministrator(), 403);
    }

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        abort_unless(auth()->user()?->fresh(['roles'])?->isSystemAdministrator() && in_array($this->kind, ['general', 'security'], true), 403);
        $security = $this->kind === 'security';
        $this->mappedLogs = AuthenticationLog::query($security)->with(['causer', 'subject'])
            ->orderByDesc('created_at')->orderByDesc('id')->get()->map(function (Activity $log) {
                $source = $log->causer instanceof User ? $log->causer->fullname().' (#'.$log->causer_id.')'
                    : ($log->causer_id ? 'Account #'.$log->causer_id.' (unavailable)' : 'Not recorded');
                $assessment = $log->properties->get('security');
                if (! $log->causer && $log->causer_id && ! empty($assessment['actor_name'])) {
                    $source = $assessment['actor_name'].' (#'.$log->causer_id.'; unavailable)';
                }
                if (! $log->causer_id && $log->subject_type === (new User)->getMorphClass() && $log->subject_id) {
                    $account = $log->subject instanceof User ? $log->subject->fullname().' (#'.$log->subject_id.')' : '#'.$log->subject_id;
                    $source = 'Affected account: '.$account.'; actor not recorded';
                }

                return [
                    'id' => $log->id, 'description' => $log->description, 'source' => $source,
                    'logged_at' => $log->created_at->format('Y-m-d H:i:s'), 'show_route' => route('system.logs.show', $log->id),
                    'risk_score' => $assessment ? ($assessment['risk_score'] ?? 'Assessment unavailable') : 'Not assessed',
                    'risk_level' => isset($assessment['risk_level']) ? ucfirst($assessment['risk_level']) : ($assessment ? 'Assessment unavailable' : 'Not assessed'),
                ];
            });
        $this->columns = collect([['data' => 'description'], ['data' => 'source'], ['data' => 'logged_at']]);
        if ($security) {
            $this->columns->push(['data' => 'risk_score'], ['data' => 'risk_level']);
        }
        $this->columns->push(['data' => 'show_route']);
        $this->dispatch('refresh-dataTable', ['dataTableId' => $this->tableId(), 'rows' => $this->mappedLogs]);
    }

    public function tableId(): string
    {
        return 'activity-logs-'.$this->kind;
    }

    public function render(): View
    {
        return view('livewire.activity-log.table');
    }
}
