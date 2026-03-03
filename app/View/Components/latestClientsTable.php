<?php

namespace App\View\Components;

use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Str;

class latestClientsTable extends Component
{
    protected $clientServices;
    /**
     * Create a new component instance.
     */
    public function __construct(ClientService $clientService)
    {
        $this->clientServices = $clientService;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $data = $this->clientServices->index('tracking_no', 'asc');
        $columns = $this->clientServices->columns($data);
        $resource = 'client';
        return view('components.latest-clients-table', compact('data', 'columns', 'resource'));
    }
}
