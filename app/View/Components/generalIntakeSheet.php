<?php

namespace App\View\Components;

use App\Models\Assistance;
use App\Models\Barangay;
use App\Models\CivilStatus;
use App\Models\District;
use App\Models\Education;
use App\Models\ModeOfAssistance;
use App\Models\Nationality;
use App\Models\Relationship;
use App\Models\Religion;
use App\Models\Sex;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class generalIntakeSheet extends Component
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
        $genders = Sex::select('id', 'name')->get()->pluck('name', 'id');
        $relationships = Relationship::select('id', 'name')->get()->pluck('name', 'id');
        $civilStatus = CivilStatus::select('id', 'name')->get()->pluck('name', 'id');
        $religions = Religion::select('id', 'name')->get()->pluck('name', 'id');
        $nationalities = Nationality::select('id', 'name')->get()->pluck('name', 'id');
        $educations = Education::select('id', 'name')->get()->pluck('name', 'id');
        $assistances = Assistance::select('id', 'name')->get()->pluck('name', 'id');
        $modes = ModeOfAssistance::select('id', 'name')->get()->pluck('name', 'id');
        $barangays = Barangay::select('id', 'name')->get()->pluck('name', 'id');
        $districts = District::select('id', 'name')->get()->pluck('name', 'id');

        return view('components.general-intake-sheet', compact(
            'genders',
            'relationships',
            'civilStatus',
            'religions',
            'nationalities',
            'educations',
            'assistances',
            'modes',
            'barangays',
            'districts'
        ));
    }
}
