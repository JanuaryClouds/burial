@extends('layouts.guest', ['recoveryPage' => true, 'guestPageTitle' => 'Invitation unavailable'])
@section('content')
<main class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="card w-100 mw-500px"><div class="card-body">
        <h1>Invitation unavailable</h1>
        <p>This invitation is invalid, expired, or already completed.</p>
        <p>If it has expired, ask your superadmin to renew it, then reopen the same link from your email.
            A revoked or completed invitation cannot be renewed.</p>
        <a href="{{ route('admin.login') }}">Go to staff sign in</a>
    </div></div>
</main>
@endsection
