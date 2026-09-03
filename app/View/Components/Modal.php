<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public string $modalId;
    public ?string $buttonClass;
    public string $modalTitle;
    public ?string $modalSize;

    /**
     * Create a new component instance.
     */
    public function __construct(string $modalId, ?string $buttonClass = '', string $modalTitle, ?string $modalSize = 'sm')
    {
        $this->modalId = $modalId;
        $this->buttonClass = $buttonClass;
        $this->modalTitle = $modalTitle;
        $this->modalSize = $modalSize;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
