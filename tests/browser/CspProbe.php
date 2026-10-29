<?php

namespace Tests\Browser;

use Livewire\Component;

// Mounted only by the guarded local browser router, never by application routes.
class CspProbe extends Component
{
    public int $count = 0;

    public int $polls = 0;

    public function tick(): void
    {
        $this->polls++;
    }

    public function increment(): void
    {
        $this->count++;
        $this->dispatch('notification:toast', ['type' => 'success', 'text' => 'CSP update succeeded']);
    }

    public function render()
    {
        return view()->file(__DIR__.'/csp-probe.blade.php');
    }
}
