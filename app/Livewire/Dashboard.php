<?php

namespace App\Livewire;

use App\Models\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public Collection $applications;

    public bool $isStaff = false;

    public function mount(bool $isStaff)
    {
        $this->isStaff = $isStaff;
        $this->loadData();
    }
    
    public function loadData()
    {
        if ($this->isStaff) {
            $this->applications = Application::index()->get();
        } else {
            $this->applications = Application::index(Auth::id())->get();
        }
    }
    
    public function render()
    {
        return view('livewire.dashboard');
    }
}
