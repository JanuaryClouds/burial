<?php

namespace App\Traits\Livewire;

trait GuardsRecordIndex
{
    public function bootGuardsRecordIndex(): void
    {
        abort_unless(auth()->user()?->can('view-record-index'), 403);
    }
}
