<?php

namespace App\Livewire\Forms;

use App\Models\SystemSetting;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SystemSettingsForm extends Form
{
    #[Validate('required|boolean')]
    public bool $walkInIntakeEnabled = false;

    #[Validate('sometimes|')]
    public ?bool $maintenanceMode;

    #[Validate('required|integer|min:0|max:4294967295')]
    public int $claimantChangeLimit = 1;

    #[Validate('required|string|max:255')]
    public ?string $socialWelfareOfficer;

    #[Validate('required|string|max:255')]
    public ?string $deptHead;

    #[Validate('required|email|max:255')]
    public ?string $deptEmail;

    public function setSettings(SystemSetting $settings): void
    {
        $this->walkInIntakeEnabled = (bool) $settings->walk_in_intake_enabled;
        $this->claimantChangeLimit = $settings->claimant_change_limit;
        $this->maintenanceMode = $settings->maintenance_mode;
        $this->socialWelfareOfficer = $settings->social_welfare_officer;
        $this->deptHead = $settings->dept_head;
        $this->deptEmail = $settings->department_email;
    }
}
