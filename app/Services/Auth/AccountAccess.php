<?php

namespace App\Services\Auth;

use App\Livewire\User\ChangePassword;
use App\Models\User;

class AccountAccess
{
    public const READ_ONLY_MESSAGE = 'Your Burial account is inactive. You can view your existing records, but cannot submit applications or make changes. Please contact CSWDO.';

    public function freshForRequest(User $user): ?User
    {
        $key = 'burial.account_user.'.$user->id;
        if (! request()->attributes->has($key)) {
            request()->attributes->set($key, $user->fresh(['roles']));
            // This is already a fresh role snapshot for this request.
            request()->attributes->set('burial.access_roles.'.$user->id, true);
        }

        return request()->attributes->get($key);
    }

    public function isActive(User $user): bool
    {
        return (bool) ($user->is_active ?? true);
    }

    public function isCitizen(User $user): bool
    {
        return filled($user->citizen_uuid) && ! $user->isInternal();
    }

    public function isInternal(User $user): bool
    {
        return blank($user->citizen_uuid) && $user->isInternal();
    }

    public function needsPasswordChange(User $user): bool
    {
        return $this->isInternal($user) && (bool) $user->password_change_required;
    }

    public function enforceComponent(object $component): void
    {
        if (! auth()->check()) {
            return;
        }
        $user = $this->freshForRequest(auth()->user());
        abort_unless($user !== null, 403);
        auth()->setUser($user);
        abort_if(filled($user->citizen_uuid) && $user->isInternal(), 403);
        abort_if($user->isInternal() && ! $this->isActive($user), 403);
        if ($this->needsPasswordChange($user)) {
            abort_unless($component instanceof ChangePassword, 403, 'Change your initial password before continuing.');
        }
    }

    public function assertWritable(): User
    {
        $actor = auth()->user();
        abort_unless($actor !== null, 403);
        $actor = $actor->fresh(['roles']);
        abort_unless($actor && $this->isActive($actor), 403, self::READ_ONLY_MESSAGE);
        abort_if(filled($actor->citizen_uuid) && $actor->isInternal(), 403);
        abort_if($this->needsPasswordChange($actor) && ! SeederAccountContext::permits($actor), 403, 'Change your initial password before continuing.');
        auth()->setUser($actor);

        return $actor;
    }

    /** A denial layer: existing ownership and operational policies still decide access. */
    public function before(User $user, string $ability, array $arguments): ?bool
    {
        if (filled($user->citizen_uuid) && $user->isInternal()) {
            return false;
        }
        if (! $this->isActive($user)) {
            if ($user->isInternal() || ! in_array($ability, ['view', 'viewAny', 'view-record-index'], true)) {
                return false;
            }
        }
        if ($this->needsPasswordChange($user) && ! SeederAccountContext::permits($user)) {
            $ownProfile = ($arguments[0] ?? null) instanceof User && $arguments[0]->id === $user->id;
            if (! $ownProfile || ! in_array($ability, ['view', 'update', 'changePassword'], true)) {
                return false;
            }
        }

        return null;
    }
}
