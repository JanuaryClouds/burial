<?php

namespace App\Services;

use App\Models\CustomField;
use App\Models\WorkflowStage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WorkflowCustomFieldService
{
    /** Return only persisted definition attributes, in canonical display order. */
    public function normalize(array $fields, string $path): array
    {
        $result = [];
        $names = [];
        foreach (array_values($fields) as $index => $field) {
            if (! is_array($field)) {
                throw ValidationException::withMessages([$path.'.'.$index => 'Each field must be an object.']);
            }
            $label = is_string($field['label'] ?? null) ? trim($field['label']) : ($field['label'] ?? null);
            $helpText = $field['help_text'] ?? null;
            if (is_string($helpText)) {
                $helpText = trim($helpText);
            }
            if ($helpText === '' || $helpText === false) {
                $helpText = null;
            }
            $name = $field['name'] ?? '';
            if (blank($name) && is_string($label)) {
                $name = Str::slug($label, '_');
            }
            $definition = [
                'name' => is_string($name) ? trim($name) : $name,
                'label' => $label, 'input_type' => $field['input_type'] ?? 'text',
                'help_text' => $helpText,
                'required' => $field['required'] ?? true, 'order_no' => $index + 1,
            ];
            $prefix = $path.'.'.$index;
            $validator = Validator::make($definition, [
                'name' => ['required', 'string', 'max:64', 'regex:/^[a-z][a-z0-9_]*$/'],
                'label' => ['required', 'string', 'max:255'],
                'help_text' => ['nullable', 'string', 'max:1000'],
                'input_type' => ['required', Rule::in(CustomField::INPUT_TYPES)],
                'required' => ['required', 'boolean'],
            ]);
            if ($validator->fails()) {
                throw ValidationException::withMessages(collect($validator->errors()->messages())
                    ->mapWithKeys(fn ($messages, $key) => [$prefix.'.'.$key => $messages])->all());
            }
            if (in_array($definition['name'], $names, true)) {
                throw ValidationException::withMessages([$prefix.'.name' => 'Field names must be unique within this stage.']);
            }
            $names[] = $definition['name'];
            $definition['required'] = (bool) $definition['required'];
            $result[] = $definition;
        }

        return $result;
    }

    public function validateValues(WorkflowStage $stage, array $values): array
    {
        $stage->loadMissing('customFields');
        if (array_diff(array_keys($values), $stage->customFields->modelKeys())) {
            throw ValidationException::withMessages(['customValues' => 'Choose fields belonging to the selected stage.']);
        }
        $rules = [];
        $attributes = [];
        foreach ($stage->customFields as $field) {
            $key = 'customValues.'.$field->uuid;
            $rules[$key] = [$field->required ? 'required' : 'nullable', 'string', 'max:255'];
            if ($field->input_type === 'number') {
                $rules[$key][] = 'regex:/^[+-]?(?:[0-9]+(?:\.[0-9]+)?|\.[0-9]+)$/D';
            } elseif ($field->input_type === 'date') {
                $rules[$key][] = 'date_format:Y-m-d';
            }
            $attributes[$key] = $field->label;
        }
        // Avoid numeric casts: identifiers and decimal precision must survive unchanged.
        Validator::make(['customValues' => $values], $rules, [], $attributes)->validate();

        return array_filter($values, fn ($value) => $value !== null && $value !== '');
    }
}
