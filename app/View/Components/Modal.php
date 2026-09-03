<?php

<<<<<<< HEAD
namespace App\View\Components\Button;
=======
namespace App\View\Components;
>>>>>>> 581362c44370ce73ef5d29456eb34ae7d29b48f3

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

<<<<<<< HEAD
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
=======
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
>>>>>>> 581362c44370ce73ef5d29456eb34ae7d29b48f3
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
<<<<<<< HEAD
        return view('components.button');
=======
        return view('components.modal');
>>>>>>> 581362c44370ce73ef5d29456eb34ae7d29b48f3
    }
}
