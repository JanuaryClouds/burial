<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\CustomFieldFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomField extends Model
{
    /** @use HasFactory<CustomFieldFactory> */
    use HasFactory, HasUuid;

    protected $fillable = ['name', 'label', 'help_text', 'input_type', 'required', 'order_no'];

    protected $casts = ['required' => 'boolean', 'order_no' => 'integer'];

    public const INPUT_TYPES = ['text', 'number', 'date', 'password'];

    public function fieldable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::saving(function (self $field) {
            $field->assertMutableStage();
            if ($field->exists && $field->isDirty(['fieldable_type', 'fieldable_id'])) {
                throw new \LogicException('Custom fields cannot be reassigned to another owner.');
            }
        });
        static::deleting(fn (self $field) => $field->assertMutableStage());
    }

    private function assertMutableStage(): void
    {
        if ($this->fieldable_type === (new WorkflowStage)->getMorphClass()) {
            $published = WorkflowStage::withTrashed()->whereKey($this->fieldable_id)
                ->whereHas('workflow', fn ($query) => $query->whereNotNull('published_at'))->exists();
            if ($published) {
                throw new \LogicException('Published custom fields are immutable. Publish a new workflow version.');
            }
            abort_unless(auth()->user()?->hasRole('superadmin'), 403);
        }
    }
}
