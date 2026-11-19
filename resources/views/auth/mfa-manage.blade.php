@extends('layouts.guest', ['recoveryPage' => true, 'guestPageTitle' => 'Authenticator security'])
@section('content')
<main class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
<div class="card w-100 mw-500px"><div class="card-body">
    <h1>Authenticator security</h1>
    <p>MFA is required for your superadmin account. Replacing your authenticator invalidates its secret and recovery codes. Regenerating recovery codes replaces all old codes. Either action revokes existing sessions and API tokens.</p>
    @foreach($errors->all() as $error)<p class="text-danger" role="alert">{{ $error }}</p>@endforeach
    <form method="POST" action="{{ route('mfa.manage') }}">@csrf
        <x-form.password name="password" label="Current password" />
        <label class="form-label" for="mfa-code">Fresh authenticator code</label>
        <input class="form-control mb-3" id="mfa-code" name="mfa_code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
        <p>A code works only once. Wait for the next code if you just used it to sign in or approve a change.</p>
        <button class="btn btn-warning mb-3" type="submit" name="action" value="replace">Replace authenticator</button>
        <button class="btn btn-primary mb-3" type="submit" name="action" value="regenerate">Generate new recovery codes</button>
    </form>
    <a href="{{ route('dashboard') }}">Back to dashboard</a>
</div></div>
</main>
@endsection
