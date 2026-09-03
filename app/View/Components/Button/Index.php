<?php

namespace App\View\Components\Button;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

<<<<<<<< HEAD:app/View/Components/Modal.php
class Modal extends Component
========
class Index extends Component
>>>>>>>> 581362c44370ce73ef5d29456eb34ae7d29b48f3:app/View/Components/Button/Index.php
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
