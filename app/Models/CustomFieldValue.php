<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\CustomFieldValueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Crypt;

class CustomFieldValue extends Model
{
    /** @use HasFactory<CustomFieldValueFactory> */
    use HasFactory, HasUuid;

    protected $fillable = ['custom_field_uuid', 'value'];

    // Keep plaintext out of automatic model/Livewire serialization.
    protected $hidden = ['value'];

    /** @return BelongsTo<CustomField, $this> */
    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class, 'custom_field_uuid', 'uuid');
    }

    public function valuable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::creating(function (self $value) {
            $field = CustomField::findOrFail($value->custom_field_uuid);
            if ($value->valuable_type === (new WorkflowHistory)->getMorphClass()) {
                $history = WorkflowHistory::with('toStage.permission')->findOrFail($value->valuable_id);
                abort_unless(auth()->user() && (auth()->user()->hasRole('superadmin') ||
                    ($history->toStage?->permission && auth()->user()->can($history->toStage->permission->name))), 403);
                if ($field->fieldable_type !== (new WorkflowStage)->getMorphClass() || $field->fieldable_id !== $history->to_stage_uuid) {
                    throw new \LogicException('The custom field must belong to the recorded target stage.');
                }
            }
            if ($field->input_type === 'password') {
                $value->value = Crypt::encryptString($value->value);
            }
        });
        static::updating(fn () => throw new \LogicException('Recorded custom values are read-only.'));
        static::deleting(fn () => throw new \LogicException('Recorded custom values must be preserved.'));
    }

    public function displayValue(): string
    {
        return $this->customField->input_type === 'password'
            ? Crypt::decryptString($this->value) : $this->value;
    }
}
