<?php

namespace Tests;

use App\Models\User;
use App\Services\Auth\AccountSession;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function actingAs(Authenticatable $user, $guard = null)
    {
        parent::actingAs($user, $guard);
        if ($user instanceof User && ($guard === null || $guard === 'web')) {
            request()->setLaravelSession(app('session.store'));
            app(AccountSession::class)->start($user, request());
        }
        // An authenticated staff fixture represents a completed staff login.
        if ($user instanceof User && $user->isInternal()) {
            session(['account.staff_hash' => $user->getAuthPassword()]);
            // actingAs represents a completed login. MFA-flow tests use raw login requests
            // or explicitly remove this proof when exercising pre-MFA sessions.
            if ($user->hasRole('superadmin') && \Illuminate\Support\Facades\Schema::hasTable('superadmin_mfa_credentials')) {
                $credential = app(\App\Services\Auth\SuperadminMfa::class)->credential($user);
                if (! $credential->confirmed_at) {
                    $credential->forceFill(['secret' => 'JBSWY3DPEHPK3PXP', 'confirmed_at' => now()])->save();
                }
                session(['mfa.proof' => ['user_id' => $user->id, 'version' => $credential->version]]);
            }
        }

        return $this;
    }
}
