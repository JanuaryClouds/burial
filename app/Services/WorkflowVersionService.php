<?php

namespace App\Services;

use App\Models\Application;
use App\Models\FuneralAssistanceType;
use App\Models\Workflow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkflowVersionService
{
    public function publish(array $data, ?Workflow $base = null): Workflow
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);

        return DB::transaction(function () use ($data, $base) {
            if ($base) {
                $this->lockGroup($base);
                $base = Workflow::whereKey($base->uuid)->lockForUpdate()->firstOrFail();
                if (! $base->isLatestVersion()) {
                    throw ValidationException::withMessages(['name' => 'A newer version was published. Reload the latest version before editing.']);
                }
            }
            $data['name'] = trim($data['name'] ?? '');
            $data['description'] = trim($data['description'] ?? '');
            $data['stages'] = array_values($data['stages'] ?? []);
            foreach ($data['stages'] as &$stage) {
                $stage['name'] = trim($stage['name'] ?? '');
                $stage['description'] = trim($stage['description'] ?? '');
            }
            unset($stage);
            Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'max:65535'],
                'stages' => ['required', 'array', 'min:1'],
                'stages.*.name' => ['required', 'string', 'max:255'],
                'stages.*.description' => ['required', 'string', 'max:65535'],
                'stages.*.permission_id' => ['required', 'string', 'max:255'],
                'stages.*.custom_fields' => ['sometimes', 'array'],
            ])->validate();
            $uuid = (string) Str::uuid();
            $group = $base ? $base->workflow_group_uuid : $uuid;
            $nameKey = mb_strtolower($data['name']);
            // A unique reservation serializes cross-family name allocation even at READ COMMITTED.
            DB::table('workflow_name_reservations')->insertOrIgnore(['name' => $nameKey, 'workflow_group_uuid' => $group]);
            $reservation = DB::table('workflow_name_reservations')->where('name', $nameKey)->lockForUpdate()->first();
            if ($reservation->workflow_group_uuid !== $group) {
                throw ValidationException::withMessages(['name' => 'This name belongs to another workflow.']);
            }
            $owners = Workflow::withTrashed()->where('name', $data['name'])->lockForUpdate()->get();
            if ($owners->contains(fn ($owner) => ! $base || $owner->workflow_group_uuid !== $base->workflow_group_uuid)) {
                throw ValidationException::withMessages(['name' => 'This name belongs to another workflow.']);
            }

            $stages = [];
            foreach ($data['stages'] as $index => $stage) {
                try {
                    $permission = app(WorkflowStageService::class)->permission($stage['permission_id']);
                } catch (ValidationException $exception) {
                    throw ValidationException::withMessages(['stages.'.$index.'.permission_id' => $exception->getMessage()]);
                }
                $stages[] = ['name' => $stage['name'], 'description' => $stage['description'],
                    'position' => $index + 1, 'permission_id' => $permission->id,
                    'custom_fields' => app(WorkflowCustomFieldService::class)->normalize(
                        $stage['custom_fields'] ?? [], 'stages.'.$index.'.custom_fields')];
            }
            if ($base && $base->name === $data['name'] && $base->description === $data['description']) {
                $original = $base->stages()->with('customFields')->orderBy('position')->get()
                    ->map(fn ($stage) => array_merge($stage->only(['name', 'description', 'position', 'permission_id']), [
                        'custom_fields' => $stage->customFields->map(fn ($field) => $field->only(['name', 'label', 'input_type', 'required', 'order_no']))->all(),
                    ]))->all();
                if ($original == $stages) {
                    return $base;
                }
            }
            $workflow = Workflow::create([
                'uuid' => $uuid,
                'workflow_group_uuid' => $base ? $base->workflow_group_uuid : $uuid,
                'version_number' => $base ? $base->version_number + 1 : 1,
                'name' => $data['name'], 'description' => $data['description'],
                'is_active' => $base ? $base->is_active : true, 'created_by' => auth()->id(),
            ]);
            foreach ($stages as $stage) {
                $fields = $stage['custom_fields'];
                unset($stage['custom_fields']);
                $createdStage = $workflow->stages()->create($stage);
                $createdStage->customFields()->createMany($fields);
            }
            $workflow->update(['published_at' => now()]);
            if ($base) {
                $ids = Workflow::where('workflow_group_uuid', $base->workflow_group_uuid)->pluck('uuid');
                FuneralAssistanceType::whereIn('workflow_uuid', $ids)->update(['workflow_uuid' => $workflow->uuid]);
            }
            ActivityLoggerService::logSuccess('Published workflow version', [
                'workflow_uuid' => $workflow->uuid, 'version_number' => $workflow->version_number,
            ]);

            return $workflow;
        }, 3);
    }

    public function lockGroup(Workflow $version): Workflow
    {
        return Workflow::withTrashed()->whereKey($version->workflow_group_uuid)->lockForUpdate()->firstOrFail();
    }

    public function setAvailability(Workflow $version, bool $active): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
        DB::transaction(function () use ($version, $active) {
            $this->lockGroup($version);
            Workflow::whereKey($version->uuid)->lockForUpdate()->firstOrFail()->update(['is_active' => $active]);
        }, 3);
    }

    public function currentRunCount(Workflow $version): int
    {
        return Application::whereHas('activeRecommendation', fn ($query) => $query->where('workflow_uuid', $version->uuid))
            ->whereDoesntHave('closure')->whereDoesntHave('rejection')
            ->whereDoesntHave('cancellation')->whereDoesntHave('referral')->count();
    }
}
