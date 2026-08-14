<?php

namespace App\Livewire\WorkflowStage;

use App\Models\Workflow;
use App\Models\WorkflowStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Permission;
use Illuminate\Support\Collection;

class Show extends Component
{
    public WorkflowStage $stage;
    public int $maxCount;
    public Collection $workflowPermissions;

    #[Validate('required|string|max:100')]
    public string $name;

    #[Validate('required|string|max:65535')]
    public string $description;

    #[Validate('nullable')]
    public ?string $permission_id = null;

    public function mount(WorkflowStage $stage, int $maxCount)
    {
        $this->stage = $stage;
        $this->maxCount = $maxCount;
        $this->name = $this->stage->name;
        $this->description = $this->stage->description;
        $this->permission_id = $this->stage->permission_id;
        $this->workflowPermissions = $this->loadPermissions();
    }

    private function loadPermissions(): Collection
    {
        return Permission::where('name', 'like', '%workflow%')
            ->get()
            ->mapWithKeys(function (Permission $permission) {
                return [$permission->id => Str::remove(['workflow.', '.update'], $permission->name)];
            });
    }

    #[On('refreshWorkflow')]
    public function loadStage()
    {
        $this->stage->refresh();
        $this->maxCount = $this->stage->workflow->stages()->count();
        $this->name = $this->stage->name;
        $this->description = $this->stage->description;
        $this->permission_id = $this->stage->permission_id;
        $this->workflowPermissions = $this->loadPermissions();
    }

    public function save()
    {
        $validated = $this->validate();

        $permissionId = $validated['permission_id'] ?? null;

        if ($permissionId !== null && $permissionId !== '') {
            $permission = Permission::where('id', $permissionId)
                ->first();

            // A tagged value that isn't an existing permission id is a
            // permission name; create the permission on the fly.
            if (is_null($permission)) {
                $permission = Permission::firstOrCreate([
                    'name' => 'workflow.' . Str::slug($permissionId),
                    'guard_name' => 'web',
                ]);
            }

            $permissionId = $permission->id;
        }

        $this->stage->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'permission_id' => $permissionId ?: null
        ]);

        $this->stage->refresh();

        session()->flash('Successfully updated stage information');
    }

    public function remove(string $uuid)
    {
        DB::transaction(function () use ($uuid) {
            $stage = WorkflowStage::where('uuid', $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            $emptyPosition = $stage->position;

            $stage->update([
                'position' => null,
                'permission_id' => null
            ]);

            $stage->delete();
            
            WorkflowStage::where('workflow_uuid', $stage->workflow_uuid)
                ->where('position', '>', $emptyPosition)
                ->decrement('position');
        });

        $this->dispatch('refreshWorkflow');
        session()->flash('Successfully deleted stage');
    }

    public function restore(string $uuid)
    {
        DB::transaction(function () use ($uuid) {
            $stage = WorkflowStage::withTrashed()
                ->where('uuid', $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            $lastPosition = WorkflowStage::withTrashed()
                ->where('workflow_uuid', $stage->workflow_uuid)
                ->whereNull('deleted_at')
                ->max('position') ?? 0;

            $stage->update([
                'position' => $lastPosition + 1,
                'permission_id' => null
            ]);

            $stage->restore();
        });

        session()->flash('Successfully restored stage');
        $this->dispatch('refreshWorkflow');
    }

    public function moveUp(string $uuid)
    {
        DB::transaction(function () use ($uuid) {
            $stage = WorkflowStage::where('uuid', $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            $previous = WorkflowStage::where('workflow_uuid', $stage->workflow_uuid)
                ->where('position', $stage->position - 1)
                ->lockForUpdate()
                ->first();

            if (!$previous) {
                return;
            }

            $currentposition = $stage->position;
            $previousPosition = $previous->position;

            $stage->update(['position' => null]);
            $previous->update(['position' => $currentposition]);
            $stage->update(['position' => $previousPosition]);
        });

        $this->stage->refresh();
        $this->dispatch('refreshWorkflow');
    }

    public function moveDown(string $uuid)
    {
        DB::transaction(function () use ($uuid) {
            $stage = WorkflowStage::where('uuid', $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            $next = WorkflowStage::where('workflow_uuid', $stage->workflow_uuid)
                ->where('position', $stage->position + 1)
                ->lockForUpdate()
                ->first();

            if (!$next) {
                return;
            }

            $currentposition = $stage->position;
            $nextPosition = $next->position;

            $stage->update(['position' => null]);
            $next->update(['position' => $currentposition]);
            $stage->update(['position' => $nextPosition]);
        });

        $this->stage->refresh();
        $this->dispatch('refreshWorkflow');
    }

    public function render()
    {
        return view('livewire.workflow-stage.show');
    }
}
