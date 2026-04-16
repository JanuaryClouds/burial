<?php

namespace App\Providers;

use App\Models\Beneficiary;
use App\Models\BurialAssistance;
use App\Models\FuneralAssistance;
use App\Models\Interview;
use App\Models\Permission;
use App\Models\TrackingCode;
use App\Models\User;
use App\Models\Client;
use App\Policies\BeneficiaryPolicy;
use App\Policies\BurialAssistancePolicy;
use App\Policies\FuneralAssistancePolicy;
use App\Policies\InterviewPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\ClientPolicy;
use App\Policies\TrackingCodePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,
        Beneficiary::class => BeneficiaryPolicy::class,
        Interview::class => InterviewPolicy::class,
        BurialAssistance::class => BurialAssistancePolicy::class,
        FuneralAssistance::class => FuneralAssistancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('superadmin')) {
                if (in_array($ability, ['update', 'delete', 'forceDelete'])) {
                    return null;
                }
                return true;
            }
            
            return null;
        });

        Gate::define('create', function ($user, $resource) {
            $allowedCreateExceptions = [
                'user',
                'role',
                'barangay',
                'relationship',
                'religion',
                'education',
                'nationality',
            ];

            if (in_array($resource, $allowedCreateExceptions)) {
                return true;
            }

            return false;
        });

        Gate::define('delete', function ($user, $resource) {
            $globalDeleteExceptions = [
                // BurialAssistance::class,
                // FuneralAssistance::class,
                // Client::class,
                // Beneficiary::class,
                Permission::class,
                // WorkflowStep::class,
                // Handler::class,
                User::class,
            ];

            if (in_array(get_class($resource), $globalDeleteExceptions)) {
                return false;
            }

            if ($resource instanceof Role && in_array($resource->id, [1])) {
                return false;
            }

            return true;
        });

        Gate::define('update', function ($user, $resource) {
            $globalUpdateExceptions = [
                // BurialAssistance::class,
                // FuneralAssistance::class,
                // Client::class,
                // Beneficiary::class,
                Permission::class,
                // Role::class,
                // WorkflowStep::class,
                // Handler::class,
            ];

            if (in_array(get_class($resource), $globalUpdateExceptions)) {
                return false;
            }

            if ($resource instanceof Role && in_array($resource->id, [1])) {
                return false;
            }

            if ($resource instanceof User && in_array($resource->id, [1])) {
                return false;
            }

            return true;
        });
    }
}
