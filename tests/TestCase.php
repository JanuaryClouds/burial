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
        }

        return $this;
    }
}
