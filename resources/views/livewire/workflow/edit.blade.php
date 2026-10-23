<div class="d-flex flex-column gap-6">
    @if ($historical)
        <div class="alert alert-info">Version {{ $workflow->version_number }} is read-only. Open the latest version below
            to prepare changes.</div>
    @else
        <div class="alert alert-info">Edit the complete draft below. Changes take effect together when you publish a new
            version. Reloading discards unsaved edits.</div>
    @endif
    <form wire:submit="save" class="d-flex flex-column gap-6">
        <x-card>
            <x-slot:header>{{ $workflow ? $workflow->label() : 'New Workflow' }}</x-slot:header>
            <x-form.input name="name" label="Workflow Name" wire:model="name" :readonly="$historical" required />
            <x-form.textarea name="description" label="Description" wire:model="description" :readonly="$historical"
                required />
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </x-card>
        <h2 class="fs-2 mb-0">Workflow Stages ({{ count($stages) }})</h2>
        @error('stages')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="row gy-4">
            @foreach ($stages as $index => $stage)
                <div class="col-12 col-xl-6" wire:key="draft-stage-{{ $stage['key'] }}">
                    <x-card>
                        <x-slot:header>Stage {{ $index + 1 }}</x-slot:header>
                        <x-form.input :name="'stages.' . $index . '.name'" label="Stage Name" wire:model="stages.{{ $index }}.name"
                            :readonly="$historical" required />
                        <x-form.textarea :name="'stages.' . $index . '.description'" label="Description"
                            wire:model="stages.{{ $index }}.description" :readonly="$historical" required />
                        @error('stages.' . $index . '.description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @php
                            $options = collect($workflowPermissions);
                            if ($stage['permission_id'] && !$options->has($stage['permission_id'])) {
                                $options->put($stage['permission_id'], $stage['permission_id']);
                            }
                        @endphp
                        <x-form.select :name="'stages.' . $index . '.permission_id'" :id="'draft-permission-' . $stage['key']" label="Stage Permission"
                            class="select-dynamic" wire:model="stages.{{ $index }}.permission_id"
                            :options="$options" :selected="$stage['permission_id']" :readonly="$historical" required />
                        @unless ($historical)
                            <x-form.input :name="'stagePositions.' . $stage['key']" label="Position" type="number" min="1"
                                :max="count($stages)" wire:model.change="stagePositions.{{ $stage['key'] }}" />
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-light"
                                    wire:click="moveStage('{{ $stage['key'] }}', -1)" @disabled($index === 0)>Move
                                    Up</button>
                                <button type="button" class="btn btn-sm btn-light"
                                    wire:click="moveStage('{{ $stage['key'] }}', 1)" @disabled($index === count($stages) - 1)>Move
                                    Down</button>
                                <x-modal :modalId="'remove-draft-stage-' . $stage['key']" buttonClass="btn-sm btn-danger" modalTitle="Remove Draft Stage"
                                    wire:ignore.self>
                                    <x-slot:triggerButton>Remove</x-slot:triggerButton>
                                    <p>Remove this stage from the draft? Previously published versions and their history are
                                        preserved.</p>
                                    <x-slot:footer>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            wire:click="removeStage('{{ $stage['key'] }}')" data-bs-dismiss="modal">Remove
                                            from Draft</button>
                                    </x-slot:footer>
                                </x-modal>
                            </div>
                        @endunless
                    </x-card>
                </div>
            @endforeach
        </div>
        @unless ($historical)
            <div><button type="button" class="btn btn-primary" wire:click="addStage">Add Draft Stage</button></div>
            @foreach ($removedStages as $key => $removed)
                <div wire:key="removed-draft-{{ $key }}" class="d-flex gap-3 align-items-center">
                    <span>{{ $removed['name'] ?: 'Unnamed stage' }} (removed from draft)</span>
                    <button type="button" class="btn btn-sm btn-light"
                        wire:click="restoreStage('{{ $key }}')">Restore at End</button>
                </div>
            @endforeach
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success"
                    wire:loading.attr="disabled">{{ $workflow ? 'Save New Version' : 'Create Workflow Version 1' }}</button>
            </div>
        @endunless
    </form>
    @if ($versions->isNotEmpty())
        <x-card>
            <x-slot:header>Version History and Availability</x-slot:header>
            <p class="text-muted">Pausing a version blocks stage processing for its current runs. Only the latest
                active, complete version can start new runs.</p>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Version</th>
                            <th>Published</th>
                            <th>Status</th>
                            <th>Current Runs</th>
                            <th>Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($versions as $version)
                            <tr wire:key="workflow-version-{{ $version->uuid }}">
                                <td><a href="{{ route('workflows.edit', $version) }}">{{ $version->label() }}</a></td>
                                <td>{{ $version->published_at?->format('M d, Y H:i') }}</td>
                                <td><span
                                        class="badge badge-{{ $version->is_active ? 'success' : 'secondary' }}">{{ $version->is_active ? 'Active' : 'Paused' }}</span>
                                </td>
                                <td>{{ $runCounts->get($version->uuid, 0) }}</td>
                                <td>
                                    @if ($version->is_active)
                                        <x-modal :modalId="'pause-version-' . $version->uuid" buttonClass="btn-sm btn-warning"
                                            modalTitle="Pause Workflow Version" wire:ignore.self>
                                            <x-slot:triggerButton>Pause Version</x-slot:triggerButton>
                                            <p>Pause {{ $version->label() }}? {{ $runCounts->get($version->uuid, 0) }}
                                                current run(s) will be unable to log stages until this version is
                                                resumed or a new recommendation restarts the application.</p>
                                            <x-slot:footer><button type="button" class="btn btn-warning"
                                                    wire:click="setAvailability('{{ $version->uuid }}', false)"
                                                    data-bs-dismiss="modal">Pause Version</button></x-slot:footer>
                                        </x-modal>
                                    @else
                                        <button type="button" class="btn btn-sm btn-success"
                                            wire:click="setAvailability('{{ $version->uuid }}', true)"
                                            wire:loading.attr="disabled">Resume Version</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</div>
