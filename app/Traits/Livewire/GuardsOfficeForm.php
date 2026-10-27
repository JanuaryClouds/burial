<?php

namespace App\Traits\Livewire;

trait GuardsOfficeForm
{
    public function bootGuardsOfficeForm(): void
    {
        $this->bootOfficeAccess();
    }
}
