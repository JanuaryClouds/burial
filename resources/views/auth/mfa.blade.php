@extends('layouts.guest', ['recoveryPage' => true, 'guestPageTitle' => 'Verify your authenticator'])
@section('content')
<main class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
<div class="card w-100 mw-500px"><div class="card-body">
    <h1>Superadmin verification</h1>
    @foreach($errors->all() as $error)<p class="text-danger" role="alert">{{ $error }}</p>@endforeach
    @if($stage === 'password')
        <p>Choose a new password before setting up your authenticator. Use at least 12 characters including uppercase, lowercase, a number and a symbol; maximum 72 bytes.</p>
        <form method="POST" action="{{ route('mfa.submit') }}">@csrf
            <input type="hidden" name="action" value="password">
            <x-form.password name="password" label="New password" />
            <x-form.password name="password_confirmation" label="Confirm new password" />
            <button class="btn btn-primary" type="submit">Change password</button>
        </form>
    @elseif($stage === 'codes')
        <h2>Save your recovery codes</h2>
        <p>Each code works once, with your password, to replace a lost authenticator. Keep them somewhere safe and separate from your phone. They cannot approve settings or account changes.</p>
        @if($codes)
            <textarea id="mfa-recovery-codes" class="form-control mb-3" readonly rows="10" aria-label="Recovery codes">{{ implode("\n", $codes) }}</textarea>
            <button class="btn btn-light mb-3" type="button" data-mfa-copy>Copy codes</button>
            <button class="btn btn-light mb-3" type="button" data-mfa-download>Download codes</button>
            <p role="status" data-mfa-status></p>
        @else
            <p>The codes have already been displayed and cannot be displayed again. If you did not save them, finish sign-in and generate a replacement set under Authenticator security.</p>
        @endif
        <form method="POST" action="{{ route('mfa.submit') }}">@csrf
            <input type="hidden" name="action" value="ack">
            <label><input type="checkbox" name="saved_codes" value="1" required> I have saved my recovery codes or understand that I must generate a replacement set.</label>
            <button class="btn btn-primary mt-3" type="submit">Finish sign-in</button>
        </form>
    @else
        @if($stage === 'enroll')
            <p>Scan this QR code with your authenticator app, then enter its six-digit code. Do not share the QR code or setup key.</p>
            <div class="text-center">{!! $qr !!}</div>
            <details class="my-3"><summary>Enter a setup key manually</summary><code class="text-break">{{ $secret }}</code></details>
        @else
            <p>Enter the current six-digit code from your authenticator app.</p>
        @endif
        <form method="POST" action="{{ route('mfa.submit') }}">@csrf
            <input type="hidden" name="action" value="{{ $stage === 'enroll' ? 'enroll' : 'challenge' }}">
            <label class="form-label" for="mfa-code">Authenticator code</label>
            <input class="form-control mb-3" id="mfa-code" name="mfa_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
            <button class="btn btn-primary" type="submit">Verify</button>
        </form>
        @if($stage === 'challenge')
            <details class="mt-4"><summary>Lost your authenticator?</summary>
                <p>Use one recovery code to start replacement. This invalidates the old authenticator and all remaining recovery codes immediately.</p>
                <form method="POST" action="{{ route('mfa.submit') }}">@csrf
                    <input type="hidden" name="action" value="recover">
                    <label class="form-label" for="recovery-code">Recovery code</label>
                    <input class="form-control mb-3" id="recovery-code" name="mfa_code" autocomplete="off" maxlength="32" required>
                    <button class="btn btn-warning" type="submit">Replace lost authenticator</button>
                </form>
            </details>
        @endif
    @endif
    <p class="mt-4">Verification expires ten minutes after password verification. <a href="{{ route('admin.login') }}">Start sign-in again</a>.</p>
</div></div>
</main>
<script src="{{ asset('js/mfa.js') }}" defer></script>
@endsection
