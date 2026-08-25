<?php

namespace App\Livewire\Interview;

use App\Models\Client;
use App\Models\Interview;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public Client $client;

    public Collection $interviews;

    public function mount(Client $client)
    {
        $this->client = $client;
        $this->interviews = $client->load('interviews')->interviews->sortByDesc('schedule');
    }

    #[On('interviewCreated')]
    public function refresh()
    {
        $this->interviews = $this->client->load('interviews')->interviews->sortByDesc('schedule');
    }

    public function markAsDone(Interview $interview)
    {
        $interview->update([
            'status' => 'done'
        ]);

        $this->refresh();
    }

    public function render()
    {
        return view('livewire.interview.index');
    }
}
