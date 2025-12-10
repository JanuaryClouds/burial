<?php

namespace App\Providers;

use App\Models\Barangay;
use App\Models\Relationship;
use App\Models\Religion;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use App\Models\Permission;
use App\Models\Handler;
use App\Models\WorkflowStep;
use App\Models\User;
use App\Models\BurialAssistance;
use App\Models\FuneralAssistance;
use App\Models\Client;
use App\Models\ClientBeneficiary;
use App\Policies\RolePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Role::class => RolePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('create', function ($user, $resource) {
            $allowedCreateExceptions = [
                User::class,
                Role::class,
                Barangay::class,
                Relationship::class,
                Religion::class,
            ];

            if (in_array(get_class($resource), $allowedCreateExceptions)) {
                return true;
            }

            return false;
        });

        Gate::define('delete', function ($user, $resource) {
            $globalDeleteExceptions = [
                // BurialAssistance::class,
                // FuneralAssistance::class,
                // Client::class,
                // ClientBeneficiary::class,
                Permission::class,
                // WorkflowStep::class,
                // Handler::class,
                User::class,
            ];

            if (in_array(get_class($resource), $globalDeleteExceptions)) {
                return false;
            }

            if ($resource instanceof Role && in_array($resource->id, [1,2,3,4])) {
                return false;
            }

            return true;
        });

        Gate::define('update', function ($user, $resource) {
            $globalUpdateExceptions = [
                // BurialAssistance::class,
                // FuneralAssistance::class,
                // Client::class,
                // ClientBeneficiary::class,
                Permission::class,
                // Role::class,
                // WorkflowStep::class,
                // Handler::class,
            ];

            if (in_array(get_class($resource), $globalUpdateExceptions)) {
                return false;
            }

            if ($resource instanceof Role && in_array($resource->id, [1,2,3])) {
                return false;
            }

            if ($resource instanceof User && in_array($resource->id, [1,2,3])) {
                return false;
            }

            return true;
        });
    }
}
