<div class="row g-6">
    <div class="col-lg-7">
        @if (session()->has('templateStatus'))
            <div class="alert alert-success" role="status">{{ session('templateStatus') }}</div>
        @endif
        <form wire:submit="save" class="d-flex flex-column gap-4">
            <h3>{{ $replacingUuid ? 'Replace Document Template' : 'Add Document Template' }}</h3>
            <div>
                <label for="template-name" class="form-label required">Document name</label>
                <input id="template-name" type="text" wire:model="name" maxlength="255" class="form-control" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="template-upload" class="form-label required">Word template (.docx)</label>
                <input id="template-upload" type="file" wire:model="upload" accept=".docx" class="form-control" required>
                <div class="form-text">Maximum 5 MB. Downloaded documents can be edited in Microsoft Word.</div>
                <div wire:loading wire:target="upload" role="status">Uploading…</div>
                @error('upload') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div>
                <button type="submit" class="btn btn-sm btn-primary" wire:loading.attr="disabled" wire:target="upload,save">{{ $replacingUuid ? 'Save Replacement' : 'Add Template' }}</button>
                @if ($replacingUuid)
                    <button type="button" wire:click="cancel" class="btn btn-sm btn-light">Cancel</button>
                @endif
            </div>
        </form>
        <div class="mt-6">
            @forelse ($templates as $template)
                <div class="border rounded p-4 mb-3" wire:key="template-{{ $template->uuid }}">
                    <strong>{{ $template->name }}</strong>
                    <div class="text-muted mb-3">{{ $template->original_filename }}</div>
                    <a href="{{ route('funeral_assistance_types.templates.download', [$funeralAssistanceType, $template]) }}" class="btn btn-sm btn-light">Download Original</a>
                    <button type="button" wire:click="replace('{{ $template->uuid }}')" class="btn btn-sm btn-light">Replace</button>
                    <button type="button" wire:click="remove('{{ $template->uuid }}')" wire:confirm="Remove this document template?" class="btn btn-sm btn-danger">Remove</button>
                </div>
            @empty
                <p class="text-muted">No accessory document templates have been added.</p>
            @endforelse
        </div>
    </div>
    <div class="col-lg-5">
        <details>
            <summary class="fw-bold mb-3">Supported placeholders</summary>
            <p>Type a placeholder into Word exactly as listed, for example <code>${client.fullname}</code>. Dates use October 27, 2026; amounts use 10,000.00. Full names use middle initials. Missing values become blank.</p>
            <p>Keep each placeholder in ordinary document text, a table, header, or footer. Use consistent formatting within a placeholder.</p>
            <dl>
                @foreach ($placeholders as $key => $definition)
                    <dt><code>{{ '${'.$key.'}' }}</code></dt>
                    <dd>{{ $definition['description'] }}</dd>
                @endforeach
            </dl>
        </details>
    </div>
</div>
