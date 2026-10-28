<?php

namespace Tests;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function actingAs(Authenticatable $user, $guard = null)
    {
        parent::actingAs($user, $guard);
        // An authenticated staff fixture represents a completed staff login.
        if ($user instanceof User && $user->isInternal()) {
            session(['account.staff_hash' => $user->getAuthPassword()]);
        }

        return $this;
    }
}
