<?php

namespace App\Services\Auth;

use App\Models\SuperadminMfaCredential;
use App\Models\User;
use App\Services\Mail\ImmediateMailService;
use App\Services\Mail\MfaSecurityPayload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use OTPHP\TOTP;
use Symfony\Component\Clock\NativeClock;

class SuperadminMfa
{
    public function credential(User $user): SuperadminMfaCredential
    {
        SuperadminMfaCredential::query()->insertOrIgnore(['user_id' => $user->id, 'version' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]);

        return SuperadminMfaCredential::where('user_id', $user->id)->firstOrFail();
    }

    public function begin(User $user): void
    {
        if (auth('web')->check()) {
            self::logoutCurrentBrowser();
        }
        $credential = $this->credential($user);
        session()->regenerate();
        session()->forget(['mfa.pending', 'mfa.proof', 'api_token']);
        session(['mfa.pending' => ['user_id' => $user->id, 'password_hash' => $user->getAuthPassword(),
            'version' => $credential->version, 'expires' => now()->addMinutes(10)->timestamp]]);
    }

    public function pendingUser(): User
    {
        $pending = session('mfa.pending', []);
        $user = User::with('roles')->find($pending['user_id'] ?? 0);
        $credential = $user ? SuperadminMfaCredential::where('user_id', $user->id)->first() : null;
        if (! $user || ! $user->is_active || filled($user->citizen_uuid) || ! $user->hasRole('superadmin')
            || ($pending['expires'] ?? 0) <= now()->timestamp
            || ! hash_equals($user->getAuthPassword(), $pending['password_hash'] ?? '')
            || ! $credential || ! hash_equals($credential->version, $pending['version'] ?? '')) {
            app(AuthenticationAudit::class)->record($user, 'MFA pending session expired or invalidated', 'rejected');
            session()->forget('mfa.pending');
            throw ValidationException::withMessages(['mfa_code' => 'Your verification session expired. Sign in again.']);
        }

        return $user;
    }

    public function hasProof(User $user): bool
    {
        if (! $user->hasRole('superadmin')) {
            return true;
        }
        $key = 'mfa.credential.'.$user->id;
        if (! request()->attributes->has($key)) {
            request()->attributes->set($key, SuperadminMfaCredential::where('user_id', $user->id)->first());
        }
        $credential = request()->attributes->get($key);
        if (! $credential?->confirmed_at) {
            return false;
        }

        return $this->matchesProof($user, $credential);
    }

    private function matchesProof(User $user, SuperadminMfaCredential $credential): bool
    {
        $token = $user->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            return is_string($token->mfa_version) && hash_equals($credential->version, $token->mfa_version);
        }

        return session('mfa.proof.user_id') === $user->id
            && hash_equals($user->getAuthPassword(), session('account.staff_hash', ''))
            && hash_equals($credential->version, session('mfa.proof.version', ''));
    }

    public function assertVerified(User $user): void
    {
        abort_unless($this->hasProof($user), 403, 'Sign in and verify your authenticator before continuing.');
    }

    public function enrollmentSecret(): string
    {
        $user = $this->pendingUser();
        abort_if($user->password_change_required || $this->credential($user)->confirmed_at, 403);
        $encrypted = session('mfa.pending.enrollment');
        if (! is_string($encrypted)) {
            $encrypted = Crypt::encryptString(TOTP::generate(new NativeClock)->getSecret());
            session(['mfa.pending.enrollment' => $encrypted]);
        }

        return Crypt::decryptString($encrypted);
    }

    /** Shared account throttle survives transaction rollbacks and all verification endpoints. */
    private function attempt(User $user, callable $operation): mixed
    {
        return Cache::lock('mfa.attempt.lock.'.$user->id, 30)->block(5, function () use ($user, $operation) {
            $key = 'mfa.attempt.'.$user->id;
            if (Cache::has($key.'.cooldown') || RateLimiter::tooManyAttempts($key, 5)) {
                app(AuthenticationAudit::class)->record($user, 'MFA verification cooldown rejected', 'rejected');
                throw ValidationException::withMessages(['mfa_code' => 'Too many verification attempts. Wait one minute.']);
            }
            try {
                return $operation();
            } catch (ValidationException $exception) {
                $failures = RateLimiter::hit($key, 60);
                if ($failures >= 5) {
                    Cache::put($key.'.cooldown', true, 60);
                }
                $this->recordFailure($user);
                throw $exception;
            }
        });
    }

    private function recordFailure(User $user): void
    {
        app(AuthenticationAudit::class)->record($user, 'MFA verification failed', 'failure',
            actor: auth()->user(), credential: false, logName: 'superadmin-security');
    }

    public function verifyCodeLocked(SuperadminMfaCredential $credential, #[\SensitiveParameter] string $code): void
    {
        $step = intdiv(now()->timestamp, 30);
        if (preg_match('/^\d{6}$/D', $code) && $credential->confirmed_at && $credential->secret) {
            $otp = self::otp($credential->secret);
            foreach ([$step, $step - 1, $step + 1] as $candidate) {
                if ($candidate > ($credential->last_used_step ?? -1) && hash_equals($otp->at($candidate * 30), $code)) {
                    $credential->forceFill(['last_used_step' => $candidate])->save();

                    return;
                }
            }
        }
        throw ValidationException::withMessages(['mfa_code' => 'Invalid, expired, or already-used code. Wait for the next authenticator code.']);
    }

    /** Code consumption and the protected write commit or roll back together. */
    public function protect(User $actor, #[\SensitiveParameter] string $code, callable $operation): mixed
    {
        $actor = $actor->fresh(['roles']);
        abort_unless($actor && $actor->is_active && blank($actor->citizen_uuid) && $actor->hasRole('superadmin'), 403);
        $this->assertVerified($actor);

        return $this->attempt($actor, fn () => DB::transaction(function () use ($actor, $code, $operation) {
            $credential = SuperadminMfaCredential::where('user_id', $actor->id)->lockForUpdate()->firstOrFail();
            abort_unless($this->matchesProof($actor, $credential), 403, 'Sign in and verify your authenticator again.');
            $this->verifyCodeLocked($credential, $code);

            return $operation();
        }));
    }

    public function changeInitialPassword(#[\SensitiveParameter] array $data): void
    {
        $user = $this->pendingUser();
        abort_unless($user->password_change_required, 403);
        validator($data, ['password' => [...PasswordPolicy::rules(), 'confirmed']])->validate();
        if (Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['password' => 'Choose a different password.']);
        }
        DB::transaction(function () use ($user, $data) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            abort_unless(hash_equals($locked->password, $user->password), 403);
            $locked->forceFill(['password' => $data['password'], 'password_change_required' => false])->save();
            $this->revoke($locked);
            $this->audit($locked, 'Owner changed initial password during MFA setup');
        });
        $user->refresh();
        session(['mfa.pending.password_hash' => $user->getAuthPassword()]);
    }

    private function newRecoveryCodes(SuperadminMfaCredential $credential): array
    {
        $codes = array_map(fn () => bin2hex(random_bytes(16)), range(1, 10));
        $credential->forceFill(['recovery_hashes' => array_map(fn ($code) => hash('sha256', $code), $codes)])->save();

        return $codes;
    }

    public function enroll(#[\SensitiveParameter] string $code): void
    {
        $user = $this->pendingUser();
        $secret = $this->enrollmentSecret();
        $codes = $this->attempt($user, fn () => DB::transaction(function () use ($user, $secret, $code) {
            $credential = SuperadminMfaCredential::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            abort_if($credential->confirmed_at || $credential->version !== session('mfa.pending.version'), 403);
            $credential->secret = $secret;
            $credential->confirmed_at = now();
            $this->verifyCodeLocked($credential, $code);
            $credential->version = (string) Str::uuid();
            $codes = $this->newRecoveryCodes($credential);
            $this->revoke($user);
            $this->audit($user, 'Authenticator enrolled');

            return $codes;
        }));
        $credential = $this->credential($user);
        session(['mfa.pending.version' => $credential->version, 'mfa.pending.verified' => true,
            'mfa.pending.ack_required' => true, 'mfa.pending.codes' => Crypt::encryptString(json_encode($codes))]);
        session()->forget('mfa.pending.enrollment');
        $this->notify($user, 'Authenticator enrolled');
    }

    public function challenge(#[\SensitiveParameter] string $code): void
    {
        $user = $this->pendingUser();
        abort_if($user->password_change_required, 403);
        $this->attempt($user, fn () => DB::transaction(function () use ($user, $code) {
            $credential = SuperadminMfaCredential::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            abort_unless($credential->version === session('mfa.pending.version'), 403);
            $this->verifyCodeLocked($credential, $code);
        }));
        session(['mfa.pending.verified' => true]);
    }

    public function finish(): void
    {
        $user = $this->pendingUser();
        abort_unless(session('mfa.pending.verified') && ! session('mfa.pending.ack_required') && ! $user->password_change_required, 403);
        DB::transaction(function () use ($user) {
            $credential = SuperadminMfaCredential::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            abort_unless($credential->confirmed_at && $credential->version === session('mfa.pending.version'), 403);
            Auth::login($user);
            session()->regenerate();
            session(['account.staff_hash' => $user->getAuthPassword(),
                'mfa.proof' => ['user_id' => $user->id, 'version' => $credential->version]]);
            $token = $user->createToken('fileserver');
            $token->accessToken->forceFill(['mfa_version' => $credential->version])->save();
            session(['api_token' => $token->plainTextToken]);
        });
        session()->forget('mfa.pending');
        app(AuthenticationAudit::class)->record($user, 'Superadmin completed MFA login', actor: $user, credential: true, logName: 'superadmin-security');
    }

    public function recover(#[\SensitiveParameter] string $code): void
    {
        $user = $this->pendingUser();
        abort_if($user->password_change_required, 403);
        $this->attempt($user, fn () => DB::transaction(function () use ($user, $code) {
            $credential = SuperadminMfaCredential::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            abort_unless($credential->version === session('mfa.pending.version'), 403);
            $hash = hash('sha256', trim($code));
            if (! preg_match('/^[a-f0-9]{32}$/D', trim($code)) || ! collect($credential->recovery_hashes ?? [])->contains(fn ($stored) => hash_equals($stored, $hash))) {
                throw ValidationException::withMessages(['mfa_code' => 'Invalid or already-used recovery code.']);
            }
            $this->resetLocked($user, $credential);
            $this->audit($user, 'Recovery code consumed; authenticator replacement required');
        }));
        $this->begin($user);
        $this->notify($user, 'MFA recovery started');
    }

    public function manage(User $user, #[\SensitiveParameter] string $password, #[\SensitiveParameter] string $code, bool $replace): void
    {
        abort_unless(auth()->id() === $user->id, 403);
        $this->attempt($user, function () use ($password, $user) {
            if (! Hash::check($password, $user->getAuthPassword())) {
                throw ValidationException::withMessages(['password' => 'The current password is incorrect.']);
            }
        });
        $codes = $this->protect($user, $code, function () use ($user, $replace) {
            $credential = SuperadminMfaCredential::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            if ($replace) {
                $this->resetLocked($user, $credential);
                $codes = null;
            } else {
                $codes = $this->newRecoveryCodes($credential);
                $credential->forceFill(['version' => (string) Str::uuid()])->save();
                $this->revoke($user);
            }
            $this->audit($user, $replace ? 'Authenticator replacement started' : 'Recovery codes regenerated');

            return $codes;
        });
        self::logoutCurrentBrowser();
        session()->invalidate();
        session()->regenerateToken();
        $this->begin($user->fresh(['roles']));
        if ($codes !== null) {
            session(['mfa.pending.verified' => true, 'mfa.pending.ack_required' => true,
                'mfa.pending.codes' => Crypt::encryptString(json_encode($codes))]);
        }
        $this->notify($user, $replace ? 'Authenticator replacement started' : 'Recovery codes regenerated');
    }

    public function resetLocked(User $user, SuperadminMfaCredential $credential): void
    {
        $credential->forceFill(['secret' => null, 'confirmed_at' => null, 'version' => (string) Str::uuid(),
            'last_used_step' => null, 'recovery_hashes' => []])->save();
        $this->revoke($user);
    }

    public function revoke(User $user): void
    {
        $user->newQuery()->whereKey($user->id)->update(['active_session_token' => hash('sha256', Str::random(64))]);
        $user->tokens()->delete();
    }

    public function audit(User $user, string $event, array $properties = []): void
    {
        activity('superadmin-security')->causedBy($user)->performedOn($user)->withProperties($properties)->log($event);
    }

    public function notify(User $user, string $event): void
    {
        try {
            app(ImmediateMailService::class)->send($user->email, new MfaSecurityPayload($user->id, $event));
        } catch (\Throwable) {
            $this->audit($user, 'Security notification could not be recorded or delivered');
        }
    }

    public static function otp(#[\SensitiveParameter] string $secret): TOTP
    {
        return TOTP::create($secret, 30, 'sha1', 6, clock: new NativeClock);
    }

    public static function logoutCurrentBrowser(): void
    {
        Auth::guard('web')->logoutCurrentDevice();
    }
}
