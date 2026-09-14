<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Display extends Component
{
	public ?string $label;

	public string $contents;

	public ?string $helpText;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $label, string $contents, ?string $helpText = null)
    {
		$this->label = $label;
		$this->contents = $contents;
		$this->helpText = $helpText;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.display');
    }
}
