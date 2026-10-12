<?php

namespace App\Livewire\Forms;

use App\Models\SystemSetting;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SystemSettingsForm extends Form
{
    #[Validate('sometimes|')]
    public ?bool $maintenanceMode;

    #[Validate('required|string|max:255')]
    public ?string $socialWelfareOfficer;

    #[Validate('required|string|max:255')]
    public ?string $deptHead;

    #[Validate('required|email|max:255')]
    public ?string $deptEmail;

    public function setSettings(?SystemSetting $settings)
    {
        $this->maintenanceMode = $settings->maintenance_mode;
        $this->socialWelfareOfficer = $settings->social_welfare_officer;
        $this->deptHead = $settings->dept_head;
        $this->deptEmail = $settings->department_email;
    }
}
