<?php

namespace App\Providers;

use App\Models\Assessment;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamily;
use App\Models\Cancellation;
use App\Models\Client;
use App\Models\Interview;
use App\Models\Recommendation;
use App\Models\Referral;
use App\Models\Rejection;
use App\Models\User;
use App\Models\WorkflowHistory;
use App\Policies\AssessmentPolicy;
use App\Policies\BeneficiaryFamilyPolicy;
use App\Policies\BeneficiaryPolicy;
use App\Policies\ClientPolicy;
use App\Policies\InterviewPolicy;
use App\Policies\RecommendationPolicy;
use App\Policies\ReferralPolicy;
use App\Policies\RejectionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\WorkflowHistoryPolicy;
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
        BeneficiaryFamily::class => BeneficiaryFamilyPolicy::class,
        Interview::class => InterviewPolicy::class,
        Assessment::class => AssessmentPolicy::class,
        Recommendation::class => RecommendationPolicy::class,
        WorkflowHistory::class => WorkflowHistoryPolicy::class,
        // Referral::class => ReferralPolicy::class,
        // Rejection::class => RejectionPolicy::class,
        // Cancellation::class => Cancellation::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('office-access', fn (User $user) => $user->hasOfficeAccess());
        Gate::define('search-applications', fn (User $user) => $user->canSearchApplications());
        Gate::define('view-record-index', fn (User $user) => ! $user->isInternal() || $user->hasOfficeAccess());
        Gate::define('view-reports', fn (User $user) => $user->canOfficeAction('View Report') || $user->canOfficeAction('View Any Report'));
        Gate::define('create-reports', fn (User $user) => $user->canOfficeAction('Create Report'));
    }
}
