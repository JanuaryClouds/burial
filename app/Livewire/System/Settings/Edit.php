<?php

namespace App\Livewire\System\Settings;

use App\Livewire\Forms\SystemSettingsForm;
use App\Models\SystemSetting;
use App\Services\ActivityLoggerService;
use App\Services\Auth\AccountAccess;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    use HasPlaceholder;

    public ?SystemSetting $settings = null;

    public SystemSettingsForm $form;

    public function mount()
    {
        $this->settings = SystemSetting::first();

        $this->form->setSettings($this->settings);
    }

    public function save()
    {
        app(AccountAccess::class)->assertWritable();
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
        try {
            $this->form->validate();
        } catch (\Exception $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            DB::transaction(function () {
                $this->settings->update([
                    'maintenance_mode' => $this->form->maintenanceMode,
                    'claimant_change_limit' => $this->form->claimantChangeLimit,
                    'social_welfare_officer' => $this->form->socialWelfareOfficer,
                    'dept_head' => $this->form->deptHead,
                    'department_email' => $this->form->deptEmail,
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully updated settings',
                ]);

                ActivityLoggerService::logSuccess('Successfully updated system settings');
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to update system settings');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.system.settings.edit');
    }
}
