<?php

namespace App\Livewire\Client;

use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public Collection $columns;

    public Collection $clients;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->clients = Client::index(
            Auth::user()->roles()->count() > 0 ? null : Auth::id()
        )->get();
    }

    public function render()
    {
        return view('livewire.client.index');
    }
}
