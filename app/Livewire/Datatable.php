<?php

namespace App\Livewire;

use Livewire\Component;

class Datatable extends Component
{
    public string $search = '';

    public int $total = 0;

    public int $perPage = 10;

    public ?int $pages = null;

    public function mount(?int $total = 0, ?int $perPage = 10)
    {
        $this->total = $total;
        $this->perPage = $perPage;
        $this->pages = $this->total / $this->perPage;
    }

    public function render()
    {
        return view('livewire.datatable');
    }
}
