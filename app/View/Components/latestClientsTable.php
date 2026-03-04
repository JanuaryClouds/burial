<?php

namespace App\View\Components;

use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use App\Services\DatatableService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Str;

class latestClientsTable extends Component
{
    protected $clientServices;
    protected $datatableServices;
    /**
     * Create a new component instance.
     */
    public function __construct(ClientService $clientService, DatatableService $datatableService)
    {
        $this->clientServices = $clientService;
        $this->datatableServices = $datatableService;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $data = $this->clientServices->index('tracking_no', 'asc');
        $columns = $this->datatableServices->getColumns($data, ['id', 'status', 'show_route']);
        $resource = 'client';
        return view('components.latest-clients-table', compact('data', 'columns', 'resource'));
    }
}
