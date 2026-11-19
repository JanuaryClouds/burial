<?php

namespace App\Services\Auth;

use App\Models\MailDelivery;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\Mail\ImmediateMailService;
use App\Services\Mail\InvitationPayload;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class InvitationService
{
    public function authorizeAdmin(): User
    {
        $actor = app(AccountAccess::class)->assertWritable();
        abort_unless($actor->isSystemAdministrator(), 403);

        return $actor;
    }

    public function roles(array $roles): array
    {
        Validator::make(['roles' => $roles], ['roles' => ['array'], 'roles.*' => ['string', 'distinct']])->validate();
        $available = Role::where('guard_name', 'web')->whereNotIn('name', ['staff', 'superadmin'])->pluck('name')->all();
        if (array_diff($roles, $available)) {
            throw ValidationException::withMessages(['roles' => 'Choose valid operational roles.']);
        }

        return array_values($roles);
    }

    public function create(string $email, array $roles): UserInvitation
    {
        $actor = $this->authorizeAdmin();
        $email = mb_strtolower(trim($email));
        Validator::make(['email' => $email], ['email' => ['required', 'email', 'max:254']])->validate();
        $roles = $this->roles($roles);
        try {
            return DB::transaction(function () use ($email, $roles, $actor) {
                $this->assertEmailAvailable($email);
                if (UserInvitation::where('pending_email', $email)->exists()) {
                    throw ValidationException::withMessages(['email' => 'An invitation already exists. Manage it in Invitations.']);
                }
                $token = Str::random(64);
                $invitation = UserInvitation::create(['email' => $email, 'pending_email' => $email,
                    'roles' => $roles, 'inviter_id' => $actor->id, 'token' => $token,
                    'token_hash' => hash('sha256', $token), 'expires_at' => now()->addDay()]);
                $this->audit($invitation, 'Invitation created', ['roles' => $roles]);

                return $invitation;
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'An invitation already exists. Manage it in Invitations.']);
        }
    }

    public function manage(UserInvitation $invitation, string $action, array $roles = []): UserInvitation
    {
        $this->authorizeAdmin();

        return DB::transaction(function () use ($invitation, $action, $roles) {
            $row = UserInvitation::whereKey($invitation->id)->lockForUpdate()->firstOrFail();
            abort_unless($row->isPending(), 409, 'This invitation has already been accepted or revoked.');
            if ($action === 'renew') {
                $this->renewLocked($row);
            } elseif ($action === 'roles') {
                $before = $row->roles;
                $row->update(['roles' => $this->roles($roles)]);
                $this->audit($row, 'Invitation roles updated', ['previous_roles' => $before, 'roles' => $row->roles]);
            } elseif ($action === 'revoke') {
                $row->update(['revoked_at' => now(), 'pending_email' => null, 'token' => null]);
                // No stored delivery needs a usable credential after revocation.
                MailDelivery::where('user_invitation_id', $row->id)->update(['payload' => null]);
                $this->audit($row, 'Invitation revoked');
            } else {
                abort(422);
            }

            return $row;
        });
    }

    public function send(UserInvitation $invitation): MailDelivery
    {
        $actor = $this->authorizeAdmin();
        $delivery = DB::transaction(function () use ($invitation) {
            $row = UserInvitation::whereKey($invitation->id)->lockForUpdate()->firstOrFail();
            abort_unless($row->isPending(), 409, 'This invitation is no longer pending.');
            // Reuse unsent records so resend cannot bypass an active delivery claim.
            $existing = MailDelivery::where('user_invitation_id', $row->id)
                ->whereIn('status', ['pending', 'sending', 'failed', 'uncertain'])->latest('id')->first();
            if ($existing) {
                return $existing;
            }

            $delivery = app(ImmediateMailService::class)->record($row->email, new InvitationPayload($row->id));
            $delivery->update(['user_invitation_id' => $row->id]);

            return $delivery;
        });

        return $delivery->wasRecentlyCreated
            ? app(ImmediateMailService::class)->deliver($delivery, $actor->id)
            : app(ImmediateMailService::class)->retry($delivery, $actor);
    }

    /** Caller holds the invitation row lock. */
    public function renewLocked(UserInvitation $row): void
    {
        $this->authorizeAdmin();
        abort_unless($row->isPending(), 409, 'This invitation is no longer pending.');
        $previous = $row->expires_at->toIso8601String();
        $row->update(['expires_at' => now()->addDay()]);
        $this->audit($row, 'Invitation renewed', ['previous_expiry' => $previous, 'expires_at' => $row->expires_at->toIso8601String()]);
    }

    public function findUsable(string $token): UserInvitation
    {
        abort_unless(preg_match('/^[A-Za-z0-9]{64}$/D', $token), 410, 'This invitation is unavailable. Contact your superadmin.');
        $row = UserInvitation::where('token_hash', hash('sha256', $token))->first();
        abort_unless($row && $row->isPending() && $row->expires_at->gt(now()), 410,
            'This invitation is unavailable or expired. Ask your superadmin to renew it.');

        return $row;
    }

    public function accept(string $token, array $data): User
    {
        $data = Validator::make($data, ['first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'], 'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:64'], 'contact_number' => ['required', 'string', 'max:32'],
            'password' => [...PasswordPolicy::rules(), 'confirmed']])->validate();

        try {
            return DB::transaction(function () use ($token, $data) {
                $found = $this->findUsable($token);
                $row = UserInvitation::whereKey($found->id)->lockForUpdate()->firstOrFail();
                abort_unless($row->isPending() && $row->expires_at->gt(now()), 410, 'This invitation is unavailable or expired.');
                $this->assertEmailAvailable($row->email);
                $roles = $this->roles($row->roles);
                $user = User::create([...$data, 'email' => $row->email, 'is_active' => true, 'password_change_required' => false]);
                $user->forceFill(['email_verified_at' => now()])->save();
                $user->syncRoles(array_merge(['staff'], $roles));
                $row->update(['accepted_at' => now(), 'user_id' => $user->id, 'pending_email' => null, 'token' => null]);
                MailDelivery::where('user_invitation_id', $row->id)->update(['payload' => null]);
                activity('account-management')->causedBy($user)->performedOn($row)->log('Invitation accepted');

                return $user;
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'An account already uses this email. Contact your superadmin.']);
        }
    }

    private function assertEmailAvailable(string $email): void
    {
        if (User::whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw ValidationException::withMessages(['email' => 'An account already uses this email.']);
        }
    }

    private function audit(UserInvitation $row, string $message, array $properties = []): void
    {
        activity('account-management')->causedBy(auth()->user())->performedOn($row)->withProperties($properties)->log($message);
    }
}
