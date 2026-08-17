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
    public ?int $position;

    public Collection $stages;

    public ?WorkflowStage $stage;

    public ?Collection $workflowPermissions = null;

    public int $maxStages;

    #[Validate('required|string|max:100')]
    public string $name;

    #[Validate('required|string|max:65535')]
    public string $description;

    #[Validate('nullable')]
    public ?string $permission_id = null;

    public function mount(?int $position, WorkflowStage $stage, Collection $workflowPermissions, int $maxStages)
    {
        $this->position = $position;
        $this->stage = $stage;
        $this->workflowPermissions = $workflowPermissions;
        $this->maxStages = $maxStages;

        $this->loadStage();
    }
    
    #[On('refreshWorkflow')]
    #[On('refreshPosition-{position}')]
    public function loadStage()
    {
        $stage = WorkflowStage::firstWhere('position', $this->position);
        
        if ($stage) {
            $this->stage = $stage;
            $this->name = $this->stage->name;
            $this->description = $this->stage->description;
            $this->permission_id = $this->stage->permission_id;
            $this->maxStages = WorkflowStage::where('workflow_uuid', $this->stage->workflow_uuid)
                ->whereNull('deleted_at')
                ->count();
        }
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

    public function remove()
    {
        $stage = $this->stage;

        DB::transaction(function () use ($stage) {
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

    #[On('restore-stage')]
    public function restore(string $uuid)
    {
        $stage = WorkflowStage::onlyTrashed()
            ->where('uuid', $uuid)
            ->first();

        if (is_null($stage)) {
            return;
        }

        DB::transaction(function () use ($stage) {
            $lastPosition = WorkflowStage::withTrashed()
                ->where('workflow_uuid', $stage->workflow_uuid)
                ->whereNull('deleted_at')
                ->max('position') ?? 0;

            $stage->update([
                'position' => $lastPosition + 1,
            ]);

            $stage->restore();
        });

        session()->flash('Successfully restored stage');
        $this->dispatch('refreshWorkflow');
    }

    public function moveUp()
    {
        $stage = $this->stage;

        $previous = WorkflowStage::where('workflow_uuid', $stage->workflow_uuid)
            ->where('position', $stage->position - 1)
            ->lockForUpdate()
            ->first();
        
        if (!$previous) {
            return;
        }
        
        DB::transaction(function () use ($stage, $previous) {
            $currentposition = $stage->position;
            $previousPosition = $previous->position;

            $stage->update(['position' => 0]);
            $previous->update(['position' => $currentposition]);
            $stage->update(['position' => $previousPosition]);
        });

        $this->dispatch('refreshPosition-'.$stage->position);
        $this->dispatch('refreshPosition-'.$previous->position);
    }

    public function moveDown()
    {
        $stage = $this->stage;

        $next = WorkflowStage::where('workflow_uuid', $stage->workflow_uuid)
            ->where('position', $stage->position + 1)
            ->lockForUpdate()
            ->first();

        if (!$next) {
            return;
        }

        DB::transaction(function () use ($stage, $next) {
            $currentposition = $stage->position;
            $nextPosition = $next->position;

            $stage->update(['position' => 0]);
            $next->update(['position' => $currentposition]);
            $stage->update(['position' => $nextPosition]);
        });

        $this->dispatch('refreshPosition-'.$stage->position);
        $this->dispatch('refreshPosition-'.$next->position);
    }

    public function render()
    {
        return view('livewire.workflow-stage.show');
    }
}
