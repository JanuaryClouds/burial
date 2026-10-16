<?php

namespace App\Livewire\Beneficiary;

use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public Collection $columns;

    public function mount(Collection $columns)
    {
        $this->columns = $columns;
    }

    public function render()
    {
        return view('livewire.beneficiary.index');
    }
}
