<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public Collection $columns;

    public Collection $applications;

    public function mount(Collection $columns)
    {
        $this->columns = $columns;
        $this->loadData();
    }

    public function loadData()
    {
        $this->applications = Application::index(
            Auth::user()->roles->isNotEmpty() ? null : Auth::user()->id
        )
        ->get();
    }

    public function render()
    {
        return view('livewire.application.index');
    }
}
