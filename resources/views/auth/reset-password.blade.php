@extends('layouts.guest', ['recoveryPage' => true])
@section('content')
<main class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card w-100 mw-500px"><div class="card-body">
        <h1>Reset your password</h1>
        <form method="POST" action="{{ route('password.update') }}" data-immediate-mail>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label for="email" class="form-label">Email</label>
            <input class="form-control mb-4 bg-light" type="email" id="email" name="email" value="{{ $email }}" readonly required maxlength="254" autocomplete="email">
            <p>Use at least 12 characters with uppercase and lowercase letters, a number, and a symbol. Maximum 72 bytes; spaces are preserved.</p>
            <x-form.password name="password" label="New password" />
            <x-form.password name="password_confirmation" label="Confirm new password" />
            <button class="btn btn-primary" type="submit" data-sending-label="Resetting…">Reset password</button>
            <p class="mt-4" role="status" aria-live="polite" data-mail-status></p>
            @foreach($errors->all() as $error)<p class="text-danger">{{ $error }}</p>@endforeach
        </form>
        <a href="{{ route('password.request') }}">Request a new link</a>
    </div></div>
</main>
@endsection
