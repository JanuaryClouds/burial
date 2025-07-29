<?php

namespace App\View\Components;

use App\Models\BurialServiceProvider;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class burialServiceProviderTable extends Component
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
        $providers = BurialServiceProvider::getAllProviders() ?? [];
        return view('components.burial-service-provider-table', compact('providers'));
    }
}
