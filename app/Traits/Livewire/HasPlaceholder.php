<?php

namespace App\Traits\Livewire;

trait HasPlaceholder
{
    public function placeholder()
    {
        return view('components.card.loading');
    }
}
