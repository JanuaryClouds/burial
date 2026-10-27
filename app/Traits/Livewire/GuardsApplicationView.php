<?php

namespace App\Traits\Livewire;

/** Recheck before public actions and rendering, including independent component requests. */
trait GuardsApplicationView
{
    public function hydrateGuardsApplicationView(): void
    {
        // Livewire restores this model from the database for each request.
        // Reauthorize before actions without repeating the restoration query.
        abort_unless(auth()->user()?->can('view', $this->application), 403);
    }

    public function renderingGuardsApplicationView(): void
    {
        abort_unless(auth()->user()?->can('view', $this->application), 403);
    }
}
