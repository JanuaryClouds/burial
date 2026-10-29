<?php

namespace App\Services\Auth;

use App\Models\User;
use Closure;

/** Explicit console-only scope; never changes the account's first-login flag. */
final class SeederAccountContext
{
    private static ?int $actorId = null;

    public static function run(Closure $callback): mixed
    {
        abort_unless(app()->runningInConsole(), 403);
        $actor = auth()->user()?->fresh(['roles']);
        $access = app(AccountAccess::class);
        abort_unless($actor && $access->isActive($actor) && $access->isInternal($actor), 403);
        $previous = self::$actorId;
        self::$actorId = $actor->id;
        try {
            return $callback();
        } finally {
            self::$actorId = $previous;
        }
    }

    public static function permits(User $user): bool
    {
        return app()->runningInConsole() && self::$actorId !== null && self::$actorId === $user->id;
    }
}
