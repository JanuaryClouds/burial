<?php

namespace App\Traits\Livewire\Form;

trait HasSelectProperties
{
    public ?string $label = null;

    public ?string $helpText = null;

    public bool $required = true;

    public array $options = [];

    public ?string $selected = null;
}
