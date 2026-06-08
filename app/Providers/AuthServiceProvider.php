<?php

namespace App\Providers;

use App\Models\Beneficiary;
use App\Models\BurialAssistance;
use App\Models\ClaimantChange;
use App\Models\Client;
use App\Models\ClientAssessment;
use App\Models\ClientRecommendation;
use App\Models\FuneralAssistance;
use App\Models\Interview;
use App\Models\Referral;
use App\Models\User;
use App\Policies\AssessmentPolicy;
use App\Policies\BeneficiaryPolicy;
use App\Policies\BurialAssistancePolicy;
use App\Policies\ClaimantChangePolicy;
use App\Policies\ClientPolicy;
use App\Policies\FuneralAssistancePolicy;
use App\Policies\InterviewPolicy;
use App\Policies\RecommendationPolicy;
use App\Policies\ReferralPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
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
        ClientAssessment::class => AssessmentPolicy::class,
        ClientRecommendation::class => RecommendationPolicy::class,
        Referral::class => ReferralPolicy::class,
        BurialAssistance::class => BurialAssistancePolicy::class,
        FuneralAssistance::class => FuneralAssistancePolicy::class,
        ClaimantChange::class => ClaimantChangePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability, $arguments) {
            // Block any user (including superadmin) from modifying/creating their own application details
            if (in_array($ability, ['create', 'update', 'delete', 'forceDelete'])) {
                foreach ((array) $arguments as $argument) {
                    if ($argument instanceof Client && $argument->user_id === $user->id) {
                        return false;
                    }
                    if ($argument instanceof Interview && $argument->client && $argument->client->user_id === $user->id) {
                        return false;
                    }
                    if ($argument instanceof ClientAssessment && $argument->client && $argument->client->user_id === $user->id) {
                        return false;
                    }
                    if ($argument instanceof ClientRecommendation && $argument->client && $argument->client->user_id === $user->id) {
                        return false;
                    }
                    if ($argument instanceof Referral && $argument->client && $argument->client->user_id === $user->id) {
                        return false;
                    }
                    if ($argument instanceof BurialAssistance && $argument->originalClaimant()->client->user_id === $user->id) {
                        return false;
                    }
                    if ($argument instanceof FuneralAssistance && $argument->client && $argument->client->user_id === $user->id) {
                        return false;
                    }
                }
            }

            if ($user->hasRole('superadmin')) {
                if (in_array($ability, ['update', 'delete', 'forceDelete'])) {
                    return null;
                }

                return true;
            }

            return null;
        });
    }
}
