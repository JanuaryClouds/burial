@extends('layouts.guest', ['recoveryPage' => true])
@section('content')
<main class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card w-100 mw-500px"><div class="card-body">
        <h1>Forgot your password?</h1>
        <p>Enter the email for your internal Burial account. Citizen accounts recover access through the Portal.</p>
        <form method="POST" action="{{ route('password.email') }}" data-immediate-mail>
            @csrf
            <label for="email" class="form-label">Email</label>
            <input class="form-control mb-4" type="email" id="email" name="email" required maxlength="254" autocomplete="email">
            <button class="btn btn-primary" type="submit" data-sending-label="Sending…">Send reset link</button>
            <p class="mt-4" role="status" aria-live="polite" data-mail-status>{{ session('success') }}</p>
            @foreach($errors->all() as $error)<p class="text-danger">{{ $error }}</p>@endforeach
        </form>
        <a href="{{ route('admin.login') }}">Back to sign in</a>
    </div></div>
</main>
@endsection
