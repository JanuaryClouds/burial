<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Counter extends Component
{
    #[Locked]
    public string $model;

    #[Locked]
    public ?string $scope = null;

    #[Locked]
    public ?string $label = null;

    #[Locked]
    public ?string $iconName = null;

    #[Locked]
    public ?int $iconPathsCount = null;

    #[Locked]
    public int $count = 0;

    #[Locked]
    public ?string $route = null;

    protected $listeners = [
        'refreshCounter' => 'loadCount',
    ];

    public function mount(
        string $model,
        ?string $scope = null,
        ?string $label = null,
        ?string $iconName = null,
        ?int $iconPathsCount = null,
        ?string $route = null
    ) {
        $this->model = $model;
        $this->scope = $scope;
        $this->label = $label;
        $this->iconName = $iconName;
        $this->iconPathsCount = $iconPathsCount;
        $this->route = $route;

        $this->loadCount();
    }

    public function loadCount()
    {
        if (! class_exists($this->model) || ! is_subclass_of($this->model, Model::class)) {
            $this->count = 0;

            return;
        }

        $query = ($this->model)::query();
        if ($this->scope && method_exists($this->model, 'scope'.ucfirst($this->scope))) {
            $query->{$this->scope}();
        }

        $this->count = $query->count();
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
