<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class WorkflowStageService
{
    /** Resolve one existing permission or a Select2 tag during atomic publication only. */
    public function permission(string $value): Permission
    {
        $value = trim($value);
        if (ctype_digit($value)) {
            $permission = Permission::whereKey($value)->where('guard_name', 'web')->first();
            if (! $permission || ! $this->isWorkflowPermission($permission->name)) {
                throw ValidationException::withMessages(['permission_id' => 'Select a Workflow permission for the web guard.']);
            }

            return $permission;
        }
        $name = preg_replace('/\s+/u', ' ', $value);
        $name = 'Workflow '.preg_replace('/^workflow[ .]+/i', '', $name);
        if ($name === 'Workflow ' || strcasecmp($value, 'Workflow') === 0 || mb_strlen($name) > 255) {
            throw ValidationException::withMessages(['permission_id' => 'Enter a permission name of up to 255 characters, including the Workflow prefix.']);
        }
        $existing = Permission::where('guard_name', 'web')->where('name', $value)->first();
        if ($existing && $this->isWorkflowPermission($existing->name)) {
            return $existing;
        }

        return Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    private function isWorkflowPermission(string $name): bool
    {
        return str_starts_with(strtolower($name), 'workflow ') || str_starts_with(strtolower($name), 'workflow.');
    }
}
