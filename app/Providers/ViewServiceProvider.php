<?php

namespace App\Providers;

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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $lookups = [
            'genders' => Sex::class,
            'relationships' => Relationship::class,
            'civilStatus' => CivilStatus::class,
            'religions' => Religion::class,
            'nationalities' => Nationality::class,
            'educations' => Education::class,
            'barangays' => Barangay::class,
            'districts' => District::class,
            'assistances' => Assistance::class,
            'modes' => ModeOfAssistance::class,
        ];

        View::composer('*', function ($view) use ($lookups) {
            foreach ($lookups as $resource => $modelClass) {
                $data = Cache::remember($resource, 60 * 12, function () use ($modelClass) {
                    return $modelClass::pluck('name', 'id');
                });

                $view->with($resource, $data);
            }
        });
    }
}
