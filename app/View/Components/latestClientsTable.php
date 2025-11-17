<?php

namespace App\View\Components;

use Closure;
use App\Models\Client;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class latestClientsTable extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $data = Client::select(
            'id',
            'tracking_no',
            'first_name',
            'middle_name',
            'last_name',
            'house_no',
            'street',
            'barangay_id',
            'contact_no',
        )->orderBy('created_at', 'desc')->take(10)->get();
        return view('components.latest-clients-table', compact('data'));
    }
}
