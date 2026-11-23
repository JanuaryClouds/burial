<?php

namespace App\Livewire\System\Settings;

use App\Livewire\Forms\SystemSettingsForm;
use App\Models\SystemSetting;
use App\Services\ActivityLoggerService;
use App\Services\Auth\AccountAccess;
use App\Services\SystemSettingService;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Edit extends Component
{
    use HasPlaceholder;

    public ?SystemSetting $settings = null;

    public SystemSettingsForm $form;

    public string $mfaCode = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    public function mount()
    {
        $this->settings = SystemSetting::firstOrFail();

        $this->form->setSettings($this->settings);
    }

    public function save()
    {
        app(AccountAccess::class)->assertWritable();
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
        try {
            $this->form->validate();
        } catch (\Exception $e) {
            $this->reset('mfaCode');
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            try {
                app(SystemSettingService::class)->update([
                    'maintenance_mode' => $this->form->maintenanceMode,
                    'walk_in_intake_enabled' => $this->form->walkInIntakeEnabled,
                    'claimant_change_limit' => $this->form->claimantChangeLimit,
                    'social_welfare_officer' => $this->form->socialWelfareOfficer,
                    'dept_head' => $this->form->deptHead,
                    'department_email' => $this->form->deptEmail,
                    'mfa_code' => $this->mfaCode,
                ]);
            } finally {
                $this->reset('mfaCode');
            }

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => 'Successfully updated settings',
            ]);

            ActivityLoggerService::logSuccess('Successfully updated system settings');
        } catch (ValidationException $exception) {
            throw $exception;
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
