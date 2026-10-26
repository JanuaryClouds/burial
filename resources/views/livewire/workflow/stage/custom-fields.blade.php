<div class="border-top pt-4 mt-4">
    <h3 class="fs-5">Additional Fields</h3>
    @error('stages.' . $index . '.custom_fields')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    @foreach ($stage['custom_fields'] as $fieldIndex => $field)
        @php($fieldPath = 'stages.' . $index . '.custom_fields.' . $fieldIndex)
        <div class="border rounded p-3 mb-3" wire:key="draft-field-{{ $field['key'] }}">
            <x-form.input :name="$fieldPath . '.label'" label="Field Label"
                wire:model.blur="stages.{{ $index }}.custom_fields.{{ $fieldIndex }}.label" :readonly="$historical"
                required />
            <x-form.textarea :name="$fieldPath . '.help_text'" label="Help Text (Optional)"
                wire:model="stages.{{ $index }}.custom_fields.{{ $fieldIndex }}.help_text"
                :readonly="$historical" maxlength="1000" />
            <x-form.input :name="$fieldPath . '.name'" label="Machine Name"
                wire:model="stages.{{ $index }}.custom_fields.{{ $fieldIndex }}.name" :readonly="$historical"
                required />
            <x-form.select :name="$fieldPath . '.input_type'" :id="'field-type-' . $field['key']" label="Input Type" :options="[
                'text' => 'Text',
                'number' => 'Number',
                'date' => 'Date',
                'password' => 'Password (concealed entry)',
            ]" :selected="$field['input_type']"
                wire:model="stages.{{ $index }}.custom_fields.{{ $fieldIndex }}.input_type" :readonly="$historical"
                required />
            <label class="d-flex gap-2 mb-3">
                <input type="checkbox"
                    wire:model="stages.{{ $index }}.custom_fields.{{ $fieldIndex }}.required"
                    @disabled($historical)>
                Required before recording processing
            </label>
            @foreach (['name', 'label', 'help_text', 'input_type', 'required'] as $attribute)
                @error($fieldPath . '.' . $attribute)
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            @endforeach
            @unless ($historical)
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light"
                        wire:click="moveField('{{ $stage['key'] }}', '{{ $field['key'] }}', -1)"
                        @disabled($fieldIndex === 0)>Move Up</button>
                    <button type="button" class="btn btn-sm btn-light"
                        wire:click="moveField('{{ $stage['key'] }}', '{{ $field['key'] }}', 1)"
                        @disabled($fieldIndex === count($stage['custom_fields']) - 1)>Move Down</button>
                    <button type="button" class="btn btn-sm btn-light-danger"
                        wire:click="removeField('{{ $stage['key'] }}', '{{ $field['key'] }}')">Remove Field</button>
                </div>
            @endunless
        </div>
    @endforeach
    @unless ($historical)
        @foreach ($stage['removed_fields'] as $removedField)
            <div class="mb-3" wire:key="removed-field-{{ $removedField['key'] }}">
                {{ $removedField['label'] ?: 'Unnamed field' }} (removed)
                <button type="button" class="btn btn-sm btn-light"
                    wire:click="restoreField('{{ $stage['key'] }}', '{{ $removedField['key'] }}')">Restore at End</button>
            </div>
        @endforeach
        <button type="button" class="btn btn-sm btn-light-primary mb-4" wire:click="addField('{{ $stage['key'] }}')">Add
            Field</button>
    @endunless
</div>
